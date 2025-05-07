<?php session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Edytor tematów JSON</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
  <link rel="icon" href="data:,">

<link href="css/add.css" rel="stylesheet">

  <style>


  </style>
</head>
<body>


  <div id="app" class="window">
<div class="controls">
  <p class="title">Albinos</p>
  <p class="subtitle">Edytor tematów JSON</p>
</div>



<div style="margin: 15px 0;">
  <button class="save" @click="toggleTabs">
    {{ showTabs ? 'Ukryj zakładki' : 'Pokaż zakładki' }}
  </button>
</div>

<div class="tab-buttons" v-if="showTabs">

  <div v-for="(tab, i) in tabs" :key="tab.id" style="display: flex; align-items: center;">
    <button @click="loadTab(tab.id)" :class="{ active: selectedTab === tab.id }">{{ tab.label }}</button>
    <button @click="moveTab(i, 'up')" :disabled="i === 0">🔼</button>
    <button @click="moveTab(i, 'down')" :disabled="i === tabs.length - 1">🔽</button>
  </div>



<!--  -->

<h3>Zarządzaj zakładkami</h3>

<div style="margin-bottom: 20px;">
  <label>Nazwa nowej zakładki:</label>
  <input v-model="newTabName" placeholder="np. blog" />

  <button class="save" @click="addTab">Dodaj zakładkę</button>
</div>

<h3>Zmień nazwę zakładki</h3>

<div style="margin-bottom: 20px;">
  <label>Obecna zakładka:</label>
  <select v-model="renameOldTab">
    <option v-for="tab in tabs" :value="tab.id">{{ tab.label }}</option>
  </select>

  <label>Nowa nazwa zakładki:</label>
  <input v-model="renameNewTab" placeholder="np. blog" />

  <button class="save" @click="renameTab" :disabled="!renameOldTab">Zmień nazwę</button>
  <button class="save" @click="deleteTab(renameOldTab)" :disabled="!renameOldTab">🗑 Usuń zakładkę</button>
</div>

</div>



    <div v-if="selectedTab">
  <h3>Tematy w: {{ selectedTabLabel }} ({{ notes.length }})</h3>

<div v-for="(note, index) in notes" :key="note.id || index" class="note-card">

<template v-for="(value, key) in note" :key="key">
  <div v-if="!['id', 'data_dodania', 'data_aktualizacji'].includes(key)">
    <label :for="`field-${index}-${key}`">{{ key }}:</label>

    <template v-if="key === 'tresc'">
      <textarea
        :id="`field-${index}-${key}`"
        v-model="note[key]"
        rows="13"
        :class="{
  'textarea-fullscreen': expandedTextareas.includes(index)
}"


      ></textarea>
      <button class="save" @click="toggleTextarea(index)">
        {{ expandedTextareas.includes(index) ? 'Zwiń' : 'Rozwiń' }}
      </button>
    </template>

    <template v-else>
      <input
        :id="`field-${index}-${key}`"
        v-model="note[key]"/>
    </template>
  </div>
</template>

<!-- 🟢 Specjalna obsługa data_aktualizacji -->
<div style="margin: 15px 0;">
  <label :for="`field-${index}-data_aktualizacji`">Data aktualizacji:</label>
  <div style="display: flex; gap: 10px; align-items: center; ">
    <input
      :id="`field-${index}-data_aktualizacji`"
      type="date"
      v-model="note.data_aktualizacji"
    />
    <button class="save" type="button" @click="resetDate(index)">Resetuj</button>
  </div>
</div>

<!-- 🟢 Specjalna obsługa data_dodania --> 
<div style="margin: 15px 0;">
  <label :for="`field-${index}-data_dodania`">Data dodania:</label>
  <div style="display: flex; gap: 10px; align-items: center;">
    <input
      :id="`field-${index}-data_dodania`"
      type="date"
      v-model="note.data_dodania"
    />
    <button class="save" type="button" @click="resetAddDate(index)">Resetuj</button>
  </div>
</div>


<div style="display: flex; gap: 10px; margin-bottom: 20px;">
  <button class="save" @click="saveExisting(index)">Zapisz zmiany</button>
  <button class="save" @click="deleteNote(index)" >🗑 Usuń temat</button>
</div>

  
</div>



      <h3>Dodaj nową wiadomość</h3>

      <label>Tytuł:</label>
<input v-model="newNote.title">

      <label>Opis:</label>
      <input v-model="newNote.description" />

      <label>Tagi (oddzielone przecinkami):</label>
      <input v-model="newNote.tags" />

      <label>Treść:</label>
	  
<textarea v-model="newNote.tresc" rows="13"></textarea>

      <button class="save" @click="submitNote">Zapisz</button>

      <div v-if="message" class="success">{{ message }}</div>



    </div>
	
 </div>

 <script>
 //dodaj ta zmienna bo wyskoczył błąd przy: clearInterval(autoSaveTimer); - w autozapisie
 let autoSaveTimer;
 
  const { createApp, ref, onMounted, computed } = Vue;

  createApp({
    setup() {
		const helpDatabase = window.helpDatabase; // <- od razu masz dostęp
      const tabs = ref([]);
      const selectedTab = ref('');
      const notes = ref([]);
      const message = ref('');
      const newNote = ref({
        title: '',
        description: '',
        tags: '',
        tresc: ''
      });


const tabToDelete = ref('');
const formError = ref('');

const newTabName = ref('');
const renameOldTab = ref('');
const renameNewTab = ref('');



const selectedTabLabel = computed(() => {
  const found = tabs.value.find(t => t.id === selectedTab.value);
  return found ? found.label : selectedTab.value;
});


const logDebug = async (msg) => {
  await fetch('log.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ msg })
  });
};


const deleteNote = async (index) => {
  const note = notes.value[index];
  if (!note || !confirm(`Czy na pewno chcesz usunąć temat "${note.title}"?`)) return;

  const payload = {
    action: 'delete_note',
    tab: selectedTab.value,
    id: note.id
  };

  const res = await fetch('save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const result = await res.json();
  if (result.success) {
    message.value = 'Usunięto temat.';
    loadTab(selectedTab.value);
  } else {
    formError.value = 'Błąd: ' + result.error;
  }
};


const validateNote = (note, errorTarget, showErrorFn) => {
  const title = (note.title || '').trim();
  const tresc = (note.tresc || '').trim();

  const hasInvalidJsonChars = (val) => /["\\]/.test(val);

  if (title.length < 3) {
    showErrorFn('title', 'Temat musi mieć co najmniej 3 znaki.');
    return false;
  }

  if (tresc.length < 10) {
    showErrorFn('tresc', 'Treść musi mieć co najmniej 10 znaków.');
    return false;
  }

  if (hasInvalidJsonChars(title)) {
    showErrorFn('title', 'Temat zawiera niedozwolone znaki (np. \\ lub ").');
    return false;
  }

  if (hasInvalidJsonChars(tresc)) {
    showErrorFn('tresc', 'Treść zawiera niedozwolone znaki (np. \\ lub ").');
    return false;
  }

  return true;
};



const resetDate = (index) => {
  const today = new Date().toISOString().slice(0, 10);
  notes.value[index].data_aktualizacji = today;
};
const resetAddDate = (index) => {
  const today = new Date().toISOString().slice(0, 10);
  notes.value[index].data_dodania = today;
};



const showTabs = ref(true);

// przycisk przełącza
const toggleTabs = () => {
  showTabs.value = !showTabs.value;
  localStorage.setItem('showTabs', showTabs.value ? '1' : '0');
};


const moveTab = async (index, direction) => {
  try {
    if (!Array.isArray(tabs.value)) throw new Error("Brak tablicy tabs");

    const newIndex = direction === 'up' ? index - 1 : index + 1;
    if (newIndex < 0 || newIndex >= tabs.value.length) return;

    const reordered = [...tabs.value];
    [reordered[index], reordered[newIndex]] = [reordered[newIndex], reordered[index]];

    const tabIds = reordered.map(t => {
      if (!t.id) throw new Error("Brak ID w jednej z zakładek");
      return t.id;
    });

    tabs.value = reordered;

    const res = await fetch('edit_tabs.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'reorder', tabs: tabIds })
    });

    const result = await res.json();
    if (!result.success) throw new Error(result.error || 'Błąd przy zapisie');
  } catch (err) {
    console.error("❌ Błąd w moveTab:", err);
    alert("Błąd przy zmianie kolejności: " + err.message);
  }
};





const renameTab = async () => {
  if (!renameOldTab.value || !renameNewTab.value.trim()) return;

  const newLabel = renameNewTab.value.trim();
  const oldTab = tabs.value.find(t => t.id === renameOldTab.value);

  if (!oldTab) {
    alert('Nie znaleziono zakładki do zmiany.');
    return;
  }

  if (oldTab.label === newLabel) {
    alert('Nowa nazwa jest taka sama jak poprzednia.');
    return;
  }

  const payload = {
    action: 'rename',
    oldId: renameOldTab.value,
    newLabel: newLabel
  };

  const response = await fetch('edit_tabs.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const result = await response.json();
  if (result.success) {
    await reloadTabs();

    const renamed = tabs.value.find(t => t.id === renameOldTab.value);
    if (renamed) renamed.label = newLabel;

    renameOldTab.value = '';
    renameNewTab.value = '';
    loadTab(renamed?.id || '');
  } else {
    alert(result.error || 'Błąd podczas zmiany nazwy zakładki');
  }
};





const reloadTabs = async () => {
  try {
    const response = await fetch('../data/dane.json');
    const jsonText = await response.text();

    const json = JSON.parse(jsonText); // sprawdzamy składnię

    tabs.value = json;
  } catch (err) {
    console.error("Błąd składni JSON:", err);
    alert("❌ Błąd w pliku JSON – nie można załadować danych.\nPopraw dane ręcznie lub zgłoś administratorowi.");
    tabs.value = []; // zapobiega błędom dalej
  }
};




const addTab = async () => {
  if (!newTabName.value.trim()) return;

  const payload = {
  action: 'add',
  label: newTabName.value.trim()
};


  const response = await fetch('edit_tabs.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const result = await response.json();
  if (result.success) {
    await reloadTabs(); // odśwież listę
    newTabName.value = '';
  } else {
    alert(result.error || 'Błąd przy dodawaniu zakładki');
  }
};


const deleteTab = async (tabId) => {
  if (!confirm(`Czy na pewno usunąć zakładkę "${tabId}"?`)) return;

  const payload = { action: 'delete', tab: tabId };

  const response = await fetch('edit_tabs.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const result = await response.json();
  if (result.success) {
    await reloadTabs();
    if (selectedTab.value === tabId) {
      selectedTab.value = '';
      notes.value = [];
    }
  } else {
    alert(result.error || 'Błąd przy usuwaniu zakładki');
  }
};


//rozwijanie textarea na max
const expandedTextareas = ref([]);
const toggleTextarea = (index) => {
  if (expandedTextareas.value.includes(index)) {
    expandedTextareas.value = expandedTextareas.value.filter(i => i !== index);
  } else {
    expandedTextareas.value.push(index);
  }
};

const generateId = () => {
  return crypto.randomUUID ? crypto.randomUUID() : Math.random().toString(36).substring(2) + Date.now();
};

const loadTab = async (tabId) => {
  try {
    const response = await fetch('../data/dane.json');
    const json = await response.json();

    const foundTab = json.find(t => t.id === tabId);
    if (!foundTab) throw new Error("Nie znaleziono zakładki o ID: " + tabId);

    selectedTab.value = foundTab.id;
    notes.value = foundTab.items.map(n => ({
      ...n,
      tags: Array.isArray(n.tags) ? n.tags.join(', ') : n.tags,
data_aktualizacji: (n.data_aktualizacji || '').slice(0, 10),
data_dodania: (n.data_dodania || '').slice(0, 10)

    }));

    localStorage.setItem('lastEditedTab', tabId);
  } catch (err) {
    console.error("❌ Błąd w loadTab:", err);
    alert("Błąd ładowania zakładki: " + err.message);
  }
};




const saveExisting = async (index) => {
  const note = notes.value[index];
  const trimmedTitle = (note.title || '').trim();
  const trimmedTresc = (note.tresc || '').trim();

  const hasInvalidJsonChars = (val) => /["\\]/.test(val);

  // Walidacja
  if (trimmedTitle.length < 3) {
    alert('Temat musi mieć co najmniej 3 znaki.');
    await logDebug(`Błąd walidacji (edycja): zbyt krótki temat`);
    return;
  }

  if (trimmedTresc.length < 10) {
    alert('Treść musi mieć co najmniej 10 znaków.');
    await logDebug(`Błąd walidacji (edycja): zbyt krótka treść`);
    return;
  }

  if (hasInvalidJsonChars(trimmedTitle)) {
    alert('Temat zawiera niedozwolone znaki.');
    await logDebug(`Błąd walidacji (edycja): temat zawiera niedozwolone znaki`);
    return;
  }

  if (hasInvalidJsonChars(trimmedTresc)) {
    alert('Treść zawiera niedozwolone znaki.');
    await logDebug(`Błąd walidacji (edycja): treść zawiera niedozwolone znaki`);
    return;
  }

  // Zapis payload
  const payload = {
    id: note.id,
    ...note,
    tags: note.tags.split(',').map(t => t.trim()),
    data_dodania: note.data_dodania || new Date().toISOString().slice(0, 10),
data_aktualizacji: note.data_aktualizacji || new Date().toISOString().slice(0, 10)
  };

  const response = await fetch('save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ tab: selectedTab.value, ...payload })
  });

  const result = await response.json();

  if (result.success) {
    message.value = 'Zaktualizowano temat!';
    loadTab(selectedTab.value);
    await logDebug(`Zapisano notatkę: ${note.id} (${note.title})`);
  } else {
    alert('Błąd: ' + result.error);
    await logDebug(`❌ Błąd zapisu notatki: ${result.error}`);
  }
};







const submitNote = async () => {
  const title = newNote.value.title.trim();
  const tresc = newNote.value.tresc.trim();

  const hasInvalidJsonChars = (val) => /["\\]/.test(val);

  if (title.length < 3) {
    alert('Temat musi mieć co najmniej 3 znaki.');
    await logDebug(`Błąd walidacji (nowa notatka): zbyt krótki temat`);
    return;
  }

  if (tresc.length < 10) {
    alert('Treść musi mieć co najmniej 10 znaków.');
    await logDebug(`Błąd walidacji (nowa notatka): zbyt krótka treść`);
    return;
  }

  if (hasInvalidJsonChars(title)) {
    alert('Temat zawiera niedozwolone znaki.');
    await logDebug(`Błąd walidacji (nowa notatka): temat zawiera niedozwolone znaki`);
    return;
  }

  if (hasInvalidJsonChars(tresc)) {
    alert('Treść zawiera niedozwolone znaki.');
    await logDebug(`Błąd walidacji (nowa notatka): treść zawiera niedozwolone znaki`);
    return;
  }

  const payload = {
    id: generateId(),
    ...newNote.value,
    tags: newNote.value.tags.split(',').map(t => t.trim()),
    data_dodania: new Date().toISOString().slice(0, 19).replace('T', ' '),
    data_aktualizacji: new Date().toISOString().slice(0, 19).replace('T', ' ')
  };

  const response = await fetch('save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ tab: selectedTab.value, ...payload })
  });

  const result = await response.json();

  if (result.success) {
    newNote.value = { title: '', description: '', tags: '', tresc: '' };
    message.value = 'Dodano nowy temat!';
    loadTab(selectedTab.value);
    await logDebug(`Dodano nową notatkę: Temat: (${payload.title})`);
  } else {
    alert('Błąd: ' + result.error);
    await logDebug(`❌ Błąd dodania notatki: ${result.error}`);
  }
};






      const autoSaveAll = async () => {
        if (!selectedTab.value || notes.value.length === 0) return;

        for (const note of notes.value) {
const payload = {
  id: note.id,
  ...note,
  tags: note.tags.split(',').map(t => t.trim()),
  data_dodania: note.data_dodania || new Date().toISOString().slice(0, 10),
  data_aktualizacji: note.data_aktualizacji || new Date().toISOString().slice(0, 10)
};

          await fetch('save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tab: selectedTab.value, ...payload })
          });
        }

console.clear();
console.log(`[Auto-zapis] Zapisano zakładkę "${selectedTab.value}" (${new Date().toLocaleTimeString()})`);

if (!window._autoSaveLogStarted) {
  console.groupCollapsed('[AUTOZAPISY]');
  window._autoSaveLogStarted = true;
}

// zapisujemy bez zamykania grupy
console.log(`✔ Zakładka "${selectedTab.value}" zapisano o ${new Date().toLocaleTimeString()}`);

      };

      onMounted(async () => {
		  
		
  
		  
  
		  const stored = localStorage.getItem('showTabs');
showTabs.value = stored !== '0'; // domyślnie true


		  
		  await reloadTabs();

  const lastTab = localStorage.getItem('lastEditedTab');
const defaultTab = tabs.value.find(t => t.id === lastTab) || tabs.value[0];

if (defaultTab) {
  loadTab(defaultTab.id);
}




        // ⏱ Auto-zapis co 20 sekund
        let autoSaveTimer = setInterval(() => {
          autoSaveAll();
        }, 60000);
		//wyłącz autozapis
		//clearInterval(autoSaveTimer);
      });


      return {
        tabs,
        selectedTab,
        notes,
        newNote,
        message,
        loadTab,
        saveExisting,
        submitNote,
		 expandedTextareas,
  toggleTextarea,
  newTabName,      // <- dodaj
  addTab,          // <- dodaj
  deleteTab,        //
    renameOldTab,     // NOWE
  renameNewTab,     // NOWE
  renameTab,
moveTab,  // NOWE
resetDate,
showTabs,
toggleTabs,
resetAddDate,


  formError,
selectedTabLabel,




  tabToDelete,
  deleteNote



  

      };
    }
  }).mount('#app');
</script>




<?php include 'debug_panel.php'; ?>


</body>
</html>
