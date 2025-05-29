<?php session_start();
?>
<?php
// Wykonujemy kopie pliku - nalezy usunac
$source = '../dane.json';
$destination = '../data/dane.json';

if (file_exists($source)) {
    copy($source, $destination);
	//echo"ok";
}
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

input[type="color"] {
  border: 1px solid #ccc;
  border-radius: 4px;
}
textarea:focus, input:focus, select:focus {
  outline: none;
  border-color: #007acc;
  box-shadow: 0 0 4px #007acc88;
}

textarea.textarea-fullscreen {
  min-height: 400px;
  font-size: 1.05em;
  line-height: 1.6;
  padding: 12px;
  background-color: ;
}

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
  <button class="save" @click="deleteTab(renameOldTab)" :disabled="!renameOldTab"><span class="iconify" :data-icon="SectionIcons.trash"></span> Usuń zakładkę</button>
</div>

</div>



    <div v-if="selectedTab">
  <h3>Tematy w: {{ selectedTabLabel }} ({{ notes.length }})</h3>


<div v-for="(note, index) in notes" :key="note.id || index" class="note-card">

<!-- Input do dodania etykiety -->
<div style="margin: 10px 0;">
  <label :for="`specialLabel-${index}`">Dodaj etykietę:</label>
  <input
    :id="`specialLabel-${index}`"
    type="text"
    v-model="note.etykieta"
    placeholder="np. ważne"
/>
  <button class="save" style="margin-left: 10px;" @click="addSpecialLabel(index)">➕ Dodaj</button>
</div>

<!-- Lista etykiet w select + usuwanie -->
<div v-if="note">
  <label>Etykiety przypisane:</label>
  <div style="display: flex; gap: 10px; align-items: center;">
    <select v-model="note.selectedLabel" :key="'select-'+index">
      <option disabled value="">-- wybierz --</option>
      <template v-for="(val, key) in note">
        <option
          v-if="String(val).trim() === 'tak' && !['title','description','tresc','tags','id','data_dodania','data_aktualizacji','images','imagetype','likes'].includes(key)"
          :key="key"
          :value="key"
        >
          {{ key }}
        </option>
      </template>
    </select>
    <button
      v-if="note.selectedLabel"
      class="save"
      @click="deleteSpecialLabel(index, note.selectedLabel)"
    >🗑 Usuń</button>
  </div>
</div>





<template v-for="(value, key) in note" :key="key">
  <div
    v-if="![
      'id',
      'data_dodania',
      'data_aktualizacji',
      'etykieta',
      'selectedLabel'
    ].includes(key) && !(String(value).trim() === 'tak' && !['title','description','tresc','tags','images','imagetype','likes'].includes(key))">

    <label :for="`field-${index}-${key}`">{{ key }}:</label>

    <template v-if="key === 'tresc'">
<textarea
  :id="`field-${index}-${key}`"
  v-model="note[key]"
  rows="13"
  @input="autoGrow"
  @dragover.prevent
  @drop="handleTextareaDrop($event, index)"
  :class="{
  'textarea-fullscreen': expandedTextareas.includes(index)
}"
></textarea>



<div style="margin-bottom: 20px;">


      <button class="save" @click="toggleTextarea(index)">
        {{ expandedTextareas.includes(index) ? 'Zwiń' : 'Rozwiń' }}
      </button>
	    <button class="save" style="color: lightgreen; background-color: #28a745;"@click="saveExisting(index)">Zapisz zmiany</button>
	  </div>
	  
	  <div v-if="perNoteMessages[index]" class="success" style="margin:20px 0;">
  {{ perNoteMessages[index] }}
</div>
<div class="insert-buttons" style="margin-top: 10px ; display: flex; flex-wrap: wrap; gap: 6px;">

<button @click="insertOrWrap(index, key, 'p')"><span class="iconify" :data-icon="SectionIcons.note"></span> Paragraf</button>
  <button @click="insertOrWrap(index, key, 'strong')"><span class="iconify" :data-icon="SectionIcons.strength"></span> Pogrubienie</button>
  <button @click="insertOrWrap(index, key, 'em')"><span class="iconify" :data-icon="SectionIcons.sparkle"></span> Kursywa</button>
  <button @click="insertOrWrap(index, key, 'h2')"><span class="iconify" :data-icon="SectionIcons.news"></span> Nagłówek</button>
  <button @click="insertOrWrap(index, key, 'blockquote')"><span class="iconify" :data-icon="SectionIcons.comment"></span> Cytat</button>
   <button @click="insertOrWrap(index, key, 'br')">↩️ Nowa linia (`br`)</button>

<button @click="wrapOrInsertList(index, key)"><span class="iconify" :data-icon="SectionIcons.clipboard"></span> Lista</button></div>

<div style="margin-nottom: 10px; display: flex; gap: 8px; align-items: center;">
  <select v-model="selectedColor" style="width:100px;">
    <option v-for="option in colorOptions" :key="option.value" :value="option.value">
      {{ option.label }}
    </option>
  </select>
  <button @click="wrapOrInsertSpan(index, key, `color: ${selectedColor}`)"><span class="iconify" :data-icon="SectionIcons.palette"></span> Zastosuj kolor</button>
</div>





	  
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

<div style="margin-top: 20px;">
  <label><span class="iconify" :data-icon="SectionIcons.folder"></span> Dodaj obrazy</label>
  <p style="font-size: 0.9em; color: #666; margin: 4px 0 10px;"><span class="iconify" :data-icon="SectionIcons.pin"></span>
   Wczytane obrazki możesz też przeciągnąć do pola tekstowego.
</p>

  <input type="file" multiple @change="handleImageUpload($event, index)" accept="image/*">

  <div v-if="note.images" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
    <div
      v-for="(img, imgIndex) in note.images.split(',').map(i => i.trim()).filter(i => i)"
      :key="imgIndex"
      style="position: relative;"
    >
<img
  :src="`../img/all_images/${img}`"
  :alt="img"
  @click="toggleImagetype(index, img)"
  :style="{
    width: '100px',
    height: 'auto',
    border: note.imagetype.includes(img) ? '3px solid green' : '1px solid #ccc',
    cursor: 'pointer'
  }"
/>

      <button
        @click.stop="removeImage(index, img)"
        style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%;"><span class="iconify" :data-icon="SectionIcons.close"></span></button>
    </div>
  </div>

<div v-if="note.imagetype.length" style="margin-top: 10px;">
  <strong>Zdjęcia przewodnie:</strong>
  {{ note.imagetype.join(', ') }}
</div>

</div>


<div style="display: flex; gap: 10px; margin: 20px 0;">

  <button class="save" @click="deleteNote(index)" ><span class="iconify" :data-icon="SectionIcons.trash"></span> Usuń temat</button>
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

    <!-- <div v-if="message" class="success">{{ message }}</div> -->



    </div>
	
 </div>

<script type="module">

 //dodaj ta zmienna bo wyskoczył błąd przy: clearInterval(autoSaveTimer); - w autozapisie
 let autoSaveTimer;

 import { createApp, ref, onMounted,computed, nextTick  } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';
  import { SectionIcons } from './src/icons.js'; // lub icons.js jeśli tak nazwałeś


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


const handleImageUpload = async (event, noteIndex) => {
  const files = event.target.files;
  if (!files.length) return;

  const formData = new FormData();
  for (const file of files) {
    formData.append('images[]', file);
  }

  const res = await fetch('upload_images.php', {
    method: 'POST',
    body: formData
  });

  const result = await res.json();
  if (result.success) {
    const uploadedNames = result.filenames;
    const current = notes.value[noteIndex].images || "";
    const currentList = current.split(',').map(i => i.trim()).filter(i => i);
    const updated = [...currentList, ...uploadedNames].join(', ');
    notes.value[noteIndex].images = updated;

const updatedList = updated.split(',').map(i => i.trim()).filter(i => i);
notes.value[noteIndex].imagetype = Array.isArray(notes.value[noteIndex].imagetype)
  ? notes.value[noteIndex].imagetype.filter(img => updatedList.includes(img))
  : [];


    showNoteMessage(noteIndex, '📁 Pliki zostały dodane');

    // ✅ automatyczny zapis zmian
    saveExisting(noteIndex);
  } else {
    alert('Błąd podczas przesyłania obrazów.');
  }
};


const perNoteMessages = ref({});
const perNoteTimers = ref({});

const showNoteMessage = (index, text) => {
  perNoteMessages.value[index] = text;

  // Jeśli poprzedni timeout istnieje — wyczyść go
  if (perNoteTimers.value[index]) {
    clearTimeout(perNoteTimers.value[index]);
  }

  // Ustaw nowy timeout i zapamiętaj go
  perNoteTimers.value[index] = setTimeout(() => {
    perNoteMessages.value[index] = '';
    perNoteTimers.value[index] = null;
  }, 3000);
};


const removeImage = (noteIndex, filename) => {
  const note = notes.value[noteIndex];
  const currentImages = note.images || "";

  // usuń z images
  const updatedList = currentImages
    .split(',')
    .map(i => i.trim())
    .filter(i => i && i !== filename);

  note.images = updatedList.join(', ');

  // usuń również z imagetype[] jeśli istnieje
  if (Array.isArray(note.imagetype)) {
    note.imagetype = note.imagetype.filter(img => img !== filename);
  }

  showNoteMessage(noteIndex, '🗑 Usunięto zdjęcie');

  saveExisting(noteIndex); // zapis zmian
};





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
  data_dodania: (n.data_dodania || '').slice(0, 10),
  imagetype: Array.isArray(n.imagetype) ? n.imagetype : (n.imagetype ? [n.imagetype] : [])
}));

    localStorage.setItem('lastEditedTab', tabId);
  } catch (err) {
    console.error("❌ Błąd w loadTab:", err);
    alert("Błąd ładowania zakładki: " + err.message);
  }
};


//duza litera
const capitalizeFirst = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const saveExisting = async (index) => {
  const note = notes.value[index];
  const trimmedTitle = (note.title || '').trim();
  const trimmedTresc = (note.tresc || '').trim();

  const hasInvalidJsonChars = (val) => false;

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

  if (hasInvalidJsonChars(trimmedTitle) || hasInvalidJsonChars(trimmedTresc)) {
    alert('Temat lub treść zawiera niedozwolone znaki.');
    await logDebug(`Błąd walidacji (edycja): niedozwolone znaki`);
    return;
  }

  let updatedList = [];

  if (note.images && note.images.trim() !== "") {
    updatedList = note.images.split(',').map(i => i.trim()).filter(i => i);
  }

  note.imagetype = Array.isArray(note.imagetype)
    ? note.imagetype.filter(img => updatedList.includes(img))
    : [];

  if (updatedList.length === 0) {
    note.imagetype = [];
  }

  const now = new Date().toISOString().slice(0, 10);

  // 🧠 DUŻA LITERA
  note.title = capitalizeFirst(note.title);
  note.description = capitalizeFirst(note.description);
  note.tresc = capitalizeFirst(note.tresc);

  // Usuń stare etykiety (jeśli chcesz nadpisywać tylko jedną – ale skoro teraz chcesz wiele, możesz to pominąć)
Object.keys(note).forEach(key => {
  if (
    note[key] === "tak" &&
    !['title', 'description', 'tresc', 'tags', 'id', 'data_dodania', 'data_aktualizacji', 'images', 'imagetype', 'likes'].includes(key)
  ) {
    // zostawiamy – nie usuwamy, bo mogą być wielokrotne etykiety
  }
});

// Dodaj nową etykietę (jeśli podano)
if (note.etykieta && note.etykieta.trim().length > 0) {
  const etykieta = note.etykieta.trim();
  note[etykieta] = "tak";
}

Reflect.deleteProperty(note, 'etykieta');
Reflect.deleteProperty(note, 'selectedLabel');

  const payload = {
    id: note.id,
    ...note,
    tags: note.tags.split(',').map(t => t.trim()),
    data_dodania: note.data_dodania || now,
    data_aktualizacji: now
  };

  const response = await fetch('save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ tab: selectedTab.value, ...payload })
  });

  const result = await response.json();

  if (result.success) {
    notes.value[index].data_aktualizacji = now;

    showNoteMessage(index, '✔ Zapisano zmiany');
    message.value = 'Zaktualizowano temat!';
    setTimeout(() => message.value = '', 3000);

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
    data_aktualizacji: new Date().toISOString().slice(0, 19).replace('T', ' '),
	  images: "",
  imagetype: "",
  likes: ""
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
    setTimeout(() => loadTab(selectedTab.value), 300);

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

const selectImagetype = (noteIndex, filename) => {
  notes.value[noteIndex].imagetype = filename;
    showNoteMessage(noteIndex, '🖼 Zdjęcie przewodnie zostało ustawione');

};


const toggleImagetype = (noteIndex, filename) => {
  const note = notes.value[noteIndex];
  if (!Array.isArray(note.imagetype)) {
    note.imagetype = [];
  }

  if (note.imagetype.includes(filename)) {
    note.imagetype = note.imagetype.filter(f => f !== filename);
  } else {
    note.imagetype.push(filename);
  }

  showNoteMessage(noteIndex, '🖼 Zaktualizowano zdjęcia przewodnie');
};


const handleTextareaDrop = async (event, noteIndex) => {
  event.preventDefault();

  const files = event.dataTransfer.files;
  if (!files || !files.length) return;

  const file = files[0];
  const formData = new FormData();
  formData.append('images[]', file);

  const res = await fetch('upload_images.php', {
    method: 'POST',
    body: formData
  });

  const result = await res.json();

  if (result.success && result.filenames.length > 0) {
    const filename = result.filenames[0];
    const path = `../img/all_images/${filename}`;
    const html = `<img src="${path}" style="display:block;margin:0 auto;width:50%;" />`;

    const textarea = event.target;

    // zabezpieczenie – upewnij się, że to textarea
    if (!textarea || typeof textarea.selectionStart !== 'number') {
      alert('Nie można ustalić pozycji kursora.');
      return;
    }

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const currentText = notes.value[noteIndex].tresc;

    // Wstaw kod <img> dokładnie w miejsce kursora
    notes.value[noteIndex].tresc =
      currentText.substring(0, start) + html + currentText.substring(end);

    // Ustaw focus i przesuń kursor za wstawionym kodem
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + html.length;

    showNoteMessage(noteIndex, '🖼 Dodano grafikę do treści');
    saveExisting(noteIndex);
  } else {
    alert('Błąd podczas przesyłania obrazka.');
  }
};




const insertOrWrap = (noteIndex, key, tag) => {
  const textarea = document.getElementById(`field-${noteIndex}-${key}`);
  if (!textarea || typeof textarea.selectionStart !== 'number') return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const value = notes.value[noteIndex][key] || '';
  const selectedText = value.substring(start, end);

  let insertText;
  let cursorPos;

  if (tag === 'br') {
    insertText = '<br>';
    cursorPos = start + insertText.length;
  } else if (selectedText) {
    insertText = `<${tag}>${selectedText}</${tag}>`;
    cursorPos = start + insertText.length;
  } else {
    insertText = `<${tag}></${tag}>`;
    cursorPos = start + tag.length + 2; // po otwarciu
  }

  // Ustawiamy nową wartość
  notes.value[noteIndex][key] = value.substring(0, start) + insertText + value.substring(end);

  // 🧠 Czekamy 1 tick, by DOM się zaktualizował
  nextTick(() => {
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = cursorPos;
  });
};




const wrapOrInsertSpan = (noteIndex, key, style) => {
  const textarea = document.getElementById(`field-${noteIndex}-${key}`);
  if (!textarea || typeof textarea.selectionStart !== 'number') return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const value = notes.value[noteIndex][key] || '';
  const selectedText = value.substring(start, end);

  let insertText;
  let cursorOffset;

  if (selectedText) {
    insertText = `<span style="${style}">${selectedText}</span>`;
    cursorOffset = insertText.length;
  } else {
    insertText = `<span style="${style}"></span>`;
    cursorOffset = insertText.length - 7; // przed zamknięciem znacznika
  }

  const newValue = value.substring(0, start) + insertText + value.substring(end);
  notes.value[noteIndex][key] = newValue;

  textarea.focus();
  textarea.selectionStart = textarea.selectionEnd = start + cursorOffset;
};


const colorOptions = [
  { label: '🟡 Żółty', value: '#ffcc00' },
  { label: '🌤 Jasnoniebieski', value: '#66ccff' },
  { label: '🌿 Miętowy', value: '#ccffcc' },
  { label: '⬜ Jasny szary', value: '#f5f5f5' },
  { label: '🔴 Czerwień (tomato)', value: 'tomato' },
  { label: '🟠 Pomarańcz (orange)', value: 'orange' },
  { label: '🟣 Fiolet (slateblue)', value: 'slateblue' },
  { label: '💧 Turkusowy (turquoise)', value: 'turquoise' },
  { label: '🟦 Indygo (indigo)', value: 'indigo' },
  { label: '🌤 Błękitny (skyblue)', value: 'skyblue' },
  { label: '🟢 Jasna zieleń (limegreen)', value: 'limegreen' },
  { label: '🌿 Morska zieleń (mediumseagreen)', value: 'mediumseagreen' }
  	
  	
  
];


const wrapOrInsertList = (noteIndex, key) => {
  const textarea = document.getElementById(`field-${noteIndex}-${key}`);
  if (!textarea || typeof textarea.selectionStart !== 'number') return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const value = notes.value[noteIndex][key] || '';
  const selectedText = value.substring(start, end);

  let insertText;

  if (selectedText) {
    const lines = selectedText.split('\n').map(line => `  <li>${line.trim()}</li>`).join('\n');
    insertText = `<ul>\n${lines}\n</ul>`;
  } else {
    insertText = `<ul>\n  <li>Punkt pierwszy</li>\n  <li>Punkt drugi</li>\n</ul>`;
  }

  const newValue = value.substring(0, start) + insertText + value.substring(end);
  notes.value[noteIndex][key] = newValue;

  textarea.focus();
  textarea.selectionStart = textarea.selectionEnd = start + insertText.length;
};


const selectedColor = ref(colorOptions[0].value); // domyślnie pierwszy kolor

const autoGrow = (e) => {
  e.target.style.height = "auto";
  e.target.style.height = (e.target.scrollHeight) + "px";
};

const deleteSpecialLabel = (index, labelKey) => {
  if (notes.value[index][labelKey] === 'tak') {
    delete notes.value[index][labelKey];
    notes.value[index].selectedLabel = '';
  }
};
const addSpecialLabel = (index) => {
  const note = notes.value[index];
  const label = (note.etykieta || '').trim();

  if (!label) return;

  if (
    ['title', 'description', 'tresc', 'tags', 'id', 'data_dodania', 'data_aktualizacji', 'images', 'imagetype', 'likes'].includes(label)
  ) {
    alert('Nie możesz użyć tej nazwy jako etykiety.');
    return;
  }

  if (note[label] !== 'tak') {
    note[label] = 'tak';
  }

  // BEZPIECZNIE wyczyść input
  nextTick(() => {
    note.etykieta = '';
  });
};



      return {
		  addSpecialLabel,
		  deleteSpecialLabel,
		  SectionIcons,
		  autoGrow,
		  wrapOrInsertList,
		  selectedColor,
colorOptions,

		  selectedColor,

		  wrapOrInsertSpan ,
insertOrWrap,
		  
		  handleTextareaDrop,
		toggleImagetype,
  perNoteTimers,

  
		  perNoteMessages,
		  showNoteMessage,
		   handleImageUpload,
  removeImage,
  selectImagetype,
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


<script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>

<?php include 'debug_panel.php'; ?>


</body>
</html>
