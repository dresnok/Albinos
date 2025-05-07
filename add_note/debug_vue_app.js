(() => {
  const { createApp, ref, onMounted } = Vue;

  createApp({
    setup() {
		
		const trashFiles = ref([]);

const loadTrashList = async () => {
  try {
    const res = await fetch('./archi.php?mode=trashlist&' + Date.now());
    if (res.ok) {
      const data = await res.json();
      trashFiles.value = data.files;
    } else {
      trashFiles.value = ['(Błąd podczas wczytywania kosza)'];
    }
  } catch (e) {
    trashFiles.value = ['(Nie udało się pobrać danych z kosza)'];
  }
};

const clearTrash = async () => {
  if (!confirm("Czy na pewno chcesz TRWALE usunąć wszystkie pliki z kosza?")) return;
  const res = await fetch('./archi.php?mode=trashclear');
  const data = await res.json();
  if (data.success) {
    //alert(`Usunięto ${data.deleted} plików z kosza.`);
    loadTrashList();
  } else {
    alert("Nie udało się opróżnić kosza.");
  }
};


		const createdAt = ref('');
const allUsers = ref([]);
const isAuthenticated = ref(false);
		const authStatus = ref('checking'); // 'ok', 'login_required', 'register_required'

		
const loginForm = ref({ username: '', password: '' });
const registerForm = ref({ username: '', password: '', repeatPassword: '', rootPassword: '' });
const authError = ref('');
const newPassword = ref('');

const changePassword = async () => {
  if (!newPassword.value.trim()) {
    authError.value = 'Hasło nie może być puste.';
    return;
  }

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'change_password', password: newPassword.value })
  });

  const data = await res.json();

  if (data.success) {
    alert('Hasło zostało zmienione.');
    newPassword.value = '';
  } else {
    authError.value = data.error || 'Błąd przy zmianie hasła.';
  }
};


const deleteAccount = async () => {
  if (!confirm('Na pewno chcesz usunąć konto? Tej operacji nie można cofnąć.')) return;

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete_account' })
  });

  const data = await res.json();

  if (data.success) {

    isAuthenticated.value = false;
    username.value = '';
    authStatus.value = 'register_required';
    activeTab.value = 'auth';
  } else {
    authError.value = data.error || 'Błąd podczas usuwania konta.';
  }
};


const logoutUser = async () => {
  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'logout' })
  });
  const data = await res.json();

  if (data.success) {
    isAuthenticated.value = false;
	username.value = '';
authStatus.value = 'login_required';
    loginForm.value.username = '';
    loginForm.value.password = '';
    activeTab.value = 'auth';
  } else {
    alert('Nie udało się wylogować.');
  }
};



const registerUser = async () => {
  const rootPassword = registerForm.value.rootPassword;

  if (!rootPassword) {
    authError.value = 'Wymagane hasło dostępu.';
    return;
  }

  authError.value = '';

  const userNameRaw = registerForm.value.username.trim(); // 👈 zamiast "const username = ..."
  const password = registerForm.value.password;
  const repeat = registerForm.value.repeatPassword;

  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'register', username: userNameRaw, password, rootPassword })
  });

  if (!userNameRaw || !password || !repeat) {
    authError.value = 'Wszystkie pola są wymagane.';
    return;
  }

  if (!/^[a-zA-Z0-9_]{3,20}$/.test(userNameRaw)) {
    authError.value = 'Nazwa użytkownika: 3-20 znaków, tylko litery, cyfry i _';
    return;
  }

  if (password.length < 4 || password.length > 50) {
    authError.value = 'Hasło musi mieć od 4 do 50 znaków.';
    return;
  }

  if (password !== repeat) {
    authError.value = 'Hasła nie są takie same.';
    return;
  }

  const data = await res.json();
  console.log('[REGISTER RESPONSE]', data);

  if (data.success) {
    isAuthenticated.value = true;
    authStatus.value = 'ok';
    username.value = data.user || userNameRaw; // 👈 tu używamy userNameRaw
    createdAt.value = data.created_at || '';
    allUsers.value = data.all_users || [];
    activeTab.value = 'logi';
  } else {
    authError.value = data.error || 'Błąd rejestracji';
  }
};



const loginUser = async () => {
  const res = await fetch('auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'login', ...loginForm.value })
  });
  const data = await res.json();
  
   console.log('[LOGIN RESPONSE]', data);
   
 if (data.success) {
  isAuthenticated.value = true;
  authStatus.value = 'ok';
  activeTab.value = 'logi';
  username.value = loginForm.value.username;

createdAt.value = data.created_at || '';
allUsers.value = data.all_users || [];
const lastBackupKey = `backup_${username.value}_date`;
const today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD

if (localStorage.getItem(lastBackupKey) !== today) {
  fetch('backup_data.php')
    .then(() => {
      localStorage.setItem(lastBackupKey, today);
      console.log('Backup wykonany.');
    })
    .catch(err => console.error('Błąd podczas wykonywania backupu:', err));
}
}
 else {
    authError.value = data.error || 'Błędne dane logowania';
  }
  
 
};



		const clearArchive = async () => {
  if (!confirm("Na pewno chcesz usunąć wszystkie pliki archiwum?")) return;
  const res = await fetch('./archi.php?mode=cleararch');
  const data = await res.json();
  if (data.success) {
    //alert(`Usunięto ${data.deleted} plików z archiwum.`);
    loadArchiveList();
  } else {
    alert("Nie udało się wyczyścić archiwum.");
  }
};

		
		const clearDebugLog = async () => {
  if (!confirm("Na pewno chcesz wyczyścić debug.log?")) return;
  await fetch('./archi.php?mode=clearlog');
  loadDebugLog();
};

const deleteArchiveFile = async (file) => {
  if (!confirm(`Usunąć plik ${file}?`)) return;
  const res = await fetch('./archi.php?mode=delete&file=' + encodeURIComponent(file));
  const data = await res.json();
  if (data.success) {
    loadArchiveList();
  } else {
    alert(data.message || 'Błąd podczas usuwania pliku');
  }
};

		const activeTab = ref('logi');
const archiveFiles = ref([]);

const loadArchiveList = async () => {
  try {
    const res = await fetch('./archi.php?mode=archive&' + Date.now());
    if (res.ok) {
      const data = await res.json();
archiveFiles.value = data.files;

    } else {
      archiveFiles.value = ['(Błąd podczas wczytywania listy plików)'];
    }
  } catch (e) {
    archiveFiles.value = ['(Nie udało się pobrać danych)'];
  }
};



	
	const isDrawerOpen = ref(false);
const debugContent = ref(''); // tutaj trzymamy logi

const toggleDrawer = () => {
  isDrawerOpen.value = !isDrawerOpen.value;
  if (isDrawerOpen.value) loadDebugLog(); // załaduj tylko po otwarciu
};

const loadDebugLog = async () => {
  const res = await fetch('debug.log?' + Date.now());
  debugContent.value = await res.text();
};

	


const username = ref('');

onMounted(async () => {
	
	 setTimeout(() => {
    const authInput = document.querySelector('.auth-tab input');
    if (authInput) {
      authInput.style.color = 'green'; // testowy wymuszony styl
    }
  }, 100);
  
  try {
    const res = await fetch('auth.php');
    const data = await res.json();

    console.log('[AUTH CHECK]', data);

    authStatus.value = data.status;

    if (data.status === 'ok') {
      isAuthenticated.value = true;
      username.value = data.user || '';
      createdAt.value = data.created_at || '';
      allUsers.value = data.all_users || [];
      activeTab.value = 'logi';
    } else {
      activeTab.value = 'auth';
    }
  } catch (err) {
    console.error('[AUTH ERROR] Nie udało się pobrać statusu z auth.php', err);
    authStatus.value = 'login_required';
    activeTab.value = 'auth';
  }
});





return {
  isDrawerOpen,
  toggleDrawer,
  debugContent,
  activeTab,
  archiveFiles,
  loadArchiveList,
  clearDebugLog,
  deleteArchiveFile,
  clearArchive,
  isAuthenticated,
  
  loginForm,
  registerForm,
  loginUser,
  registerUser,
  logoutUser,
  authError,
  username,
  authStatus,
  newPassword,
  changePassword,
  deleteAccount,
  createdAt,
  allUsers,
  trashFiles,
loadTrashList,
clearTrash

};

    }
  }).mount('#debug-app');
  })();