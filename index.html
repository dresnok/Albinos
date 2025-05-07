<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Przeglądarka Zakładek</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <link rel="icon" href="data:,">
  <style>
body {
  margin: 0;
  font-family: Ubuntu, sans-serif;
  height: 100vh;
}

#app {
  height: 100vh;
  display: flex;
}

/* Pasek zakładek */
.top-bar {
  background: #333;
  color: white;
  padding: 10px;
  display: flex;
  gap: 10px;
}

.top-bar h3 {
  margin: 0;
  font-size: 18px;
}

.top-bar button {
  background: #fff;
  color: #333;
  border: 1px solid #ccc;
  padding: 6px 12px;
  font-size: 14px;
  cursor: pointer;
  border-radius: 4px;
}

.top-bar button.active {
  background-color: #dfefff;
  font-weight: bold;
}

/* Główna zawartość */
.content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}

/* Notatki */
.note {
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 15px;
  background: #fff;
}

.meta {
  font-size: 12px;
  color: #666;
}

/*
 przykład: menu poziome 
*/
#app.menu-horizontal {
  flex-direction: column;
}

.menu-horizontal .top-bar {
  flex-direction: row;
  align-items: center;
  flex-wrap: wrap;
}

.menu-horizontal .top-bar h3 {
  margin-right: auto;
}


/*
 przykład: menu pionowe
 */
#app.menu-vertical-left {
  flex-direction: row;
}

.menu-vertical-left .top-bar {
  flex-direction: column;
  width: 200px;
  height: 100vh;
  border-right: 1px solid #ccc;
}

.menu-vertical-left .content {
  flex: 1;
}


  </style>
</head>
<body>
  <div id="app" class="menu-vertical-left">
    <div class="top-bar">
      <h3>Zakładki</h3>
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="selectTab(tab)"
        :class="{ active: selectedTab && selectedTab.id === tab.id }"
      >
        {{ tab.label }}
      </button>
    </div>

    <div class="content">
      <h2 v-if="selectedTab">Tematy w: {{ selectedTab.label }}</h2>
      <div v-if="selectedTab && selectedTab.items.length === 0">
        Brak tematów w tej zakładce.
      </div>
<div v-for="note in selectedTab?.items || []" :key="note.id" class="note">
  <h4>{{ note.title }}</h4>
  <!-- <p><strong>Opis:</strong> {{ note.description || '—' }}</p> -->
  <p><strong>Tagi:</strong> {{ formatTags(note.tags) }}</p>
  <div class="meta">
    <span>Dodano: {{ note.data_dodania }}</span><br />
    <span>Aktualizacja: {{ note.data_aktualizacji }}</span>
  </div>
  <p>{{ note.tresc }}</p>
</div>

    </div>
  </div>

  <script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
      setup() {
        const tabs = ref([]);
        const selectedTab = ref(null);

        const selectTab = (tab) => {
          selectedTab.value = tab;
        };

        const loadData = async () => {
          try {
            const response = await fetch('data/dane.json');
            const data = await response.json();
            tabs.value = data;
            selectedTab.value = data[0]; // automatycznie wybierz pierwszą
          } catch (err) {
            console.error("Błąd podczas wczytywania JSON:", err);
            alert("Nie udało się wczytać danych.");
          }
        };

const formatTags = (tags) => {
  if (Array.isArray(tags)) return tags.filter(t => t).join(', ');
  if (typeof tags === 'string') return tags;
  return '';
};


        onMounted(loadData);

        return {
          tabs,
  selectedTab,
  selectTab,
  formatTags
        };
      }
    }).mount('#app');
  </script>
</body>
</html>
