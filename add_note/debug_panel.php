<link href="css/debug_log_panel.css" rel="stylesheet">






<div id="debug-app" class="">


  <div class="drawer" :class="{ visible: isDrawerOpen }">
    <div class="drawer-header">
      <strong>Panel informacyjny</strong>
      <button class="close-btn" @click="toggleDrawer">×</button>
    </div>

    <div class="drawer-content">
      <div class="tabs">
        <button @click="activeTab = 'logi'" :class="{ active: activeTab === 'logi' }">Dziennik</button>
		

        <button @click="activeTab = 'archiwum'; loadArchiveList()" :class="{ active: activeTab === 'archiwum' }">Archiwum</button>
		<button @click="activeTab = 'trash'; loadTrashList()" :class="{ active: activeTab === 'trash' }">Kosz</button>

		<button @click="activeTab = 'auth'" :class="{ active: activeTab === 'auth' }">Logowanie / Rejestracja</button>
<button @click="activeTab = 'readme'" :class="{ active: activeTab === 'readme' }">Informacje</button>

      </div>
<div v-if="activeTab === 'trash'" class="trash-tab">
  <h4>🗑️ Pliki w koszu</h4>
  <ul class="archive-list">
    <li v-for="file in trashFiles" :key="file" class="archive-item">
      {{ file }}
    </li>
  </ul>
  <button @click="clearTrash" class="btn-action" style="background: #ff6b6b;">🧨 Usuń wszystko z kosza</button>
</div>

<div v-if="activeTab === 'readme'" class="readme-tab">
  <h3>📘 Informacje o projekcie</h3>
  <p>
    Ten projekt umożliwia przeglądanie i edytowanie danych w formacie JSON w prostym interfejsie przeglądarkowym.
  </p>
  <ul>
    <li>✔ <strong>Zakładki</strong> (np. "Blog", "Projekty") służą do grupowania notatek. Można je dodawać, zmieniać ich nazwy, przesuwać i usuwać.</li>
    <li>✔ <strong>Notatki</strong> to wpisy tekstowe z polami takimi jak <code>title</code>, <code>description</code>, <code>tags</code>, <code>tresc</code>. Można je edytować w locie, dane są natychmiast widoczne.</li>
    <li>✔ Notatki są przechowywane w pliku <code>data/dane.json</code>, każda przypisana do jednej zakładki.</li>
    <li>✔ Zmiany są zapisywane automatycznie lub ręcznie przyciskiem 💾.</li>
    <li>✔ <strong>Dziennik</strong> (zakładka "Logi") pokazuje zdarzenia systemowe z pliku <code>debug.log</code>.</li>
    <li>✔ <strong>Archiwum</strong> przechowuje kopie pliku <code>dane.json</code>, które są tworzone automatycznie raz dziennie po zalogowaniu użytkownika.</li>
    <li>✔ <strong>Logowanie</strong> nie jest wymagane, chyba że istnieje użytkownik w pliku <code>add_note/user/config.json</code>. Usuń plik lub użytkowników, by wyłączyć autoryzację.</li>
    <li>✔ Konta użytkowników można usuwać z poziomu interfejsu — nie wpływa to na dane notatek.</li>
  </ul>
  <p>
    Projekt jest rozwijany i będzie wzbogacany o kolejne funkcje.
  </p>
</div>



      <div v-if="activeTab === 'logi'">
	  
        <pre id="logOutput">{{ debugContent }}</pre>
		<button @click="clearDebugLog" class="btn-action">🧹 Wyczyść log</button>
      </div>
	  
<div v-if="activeTab === 'auth'" class="auth-tab">
  <div v-if="!isAuthenticated">
    <h3>Zaloguj się</h3>
<form @submit.prevent="loginUser">
  <input v-model="loginForm.username" placeholder="Użytkownik" autocomplete="username" />
  <input v-model="loginForm.password" type="password" placeholder="Hasło" autocomplete="new-password" />
  <button type="submit">Zaloguj</button>
</form>


<h3>Lub zarejestruj</h3>
<form @submit.prevent="registerUser">

 
  <input type="password" v-model="registerForm.rootPassword" autocomplete="new-password" placeholder="Hasło dostępu (np. root)">



  <input v-model="registerForm.username" placeholder="Nowy użytkownik" autocomplete="username" />
  <input v-model="registerForm.password" type="password" placeholder="Hasło" autocomplete="new-password" />
  <input v-model="registerForm.repeatPassword" type="password" placeholder="Powtórz hasło" autocomplete="new-password" />
  <button type="submit">Zarejestruj</button>
</form>

<div class="error" v-if="authError">{{ authError }}</div>

  </div>

  <div v-else>
<!-- Informacja o użytkowniku -->
<p>Zalogowano jako: {{ username }}</p>
<p>Data utworzenia konta: {{ createdAt }}</p>

<h4>Lista użytkowników:</h4>
<ul>
  <li v-for="u in allUsers" :key="u.username">
    {{ u.username }} – założone: {{ u.created_at }}
  </li>
</ul>


<button @click="logoutUser">Wyloguj</button>

<!-- Zmiana hasła -->
<h4>Zmiana hasła</h4>
<form @submit.prevent="changePassword">
  <input type="text" :value="username" autocomplete="username" hidden />

  <input v-model="newPassword" type="password" placeholder="Nowe hasło" autocomplete="new-password" />
  <button type="submit">Zmień hasło</button>
</form>

<!-- Usunięcie konta -->
<h4>Usuń konto</h4>
<button @click="deleteAccount" style="color: red;">🗑️ Usuń konto</button>

  </div>
</div>



	  

<div v-if="activeTab === 'archiwum'" class="archive-tab">
  
  <ul class="archive-list">
    <li v-for="file in archiveFiles" :key="file" class="archive-item">
      <a :href="'./arch/' + file" target="_blank" class="archive-link">{{ file }}</a>
    </li>
  </ul>
  <button @click="clearArchive" class="btn-action">🧨 Wyczyść archiwum</button>
</div>



   
<div id="footer-placeholder">
  <?php
    echo file_get_contents("http://company12.atwebpages.com/footer.php");
  ?>
</div>
	  
    </div>
  </div>

  <button class="drawer-toggle" @click="toggleDrawer">
    {{ isDrawerOpen ? 'Ukryj panel' : 'Pokaż logi / info' }}
  </button>


</div>
<script src="debug_vue_app.js"></script>