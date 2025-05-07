# Edytor danych JSON z zakładkami (Vue 3)

Prosty edytor danych JSON oparty o Vue 3 z możliwością przeglądania i edycji treści poprzez zakładki (`tabs`). Kod pozwala w łatwy sposób modyfikować plik `dane.json` (dodawanie, edytowanie, usuwanie tematów), a interfejs użytkownika można elastycznie dostosować do preferowanego układu menu (poziomego lub pionowego) – jedynie przez zmianę klas CSS.

---

## 📁 Struktura projektu

```
│   index.php                  ← Strona przeglądu JSON
│
├───add_note/                 ← Główny folder edytora
│   │   add.php               ← Interfejs edycji JSON
│   │   debug_panel.php       ← Panel debugowania i użytkowników
│   │   login.php             ← Logowanie użytkowników
│   │   register.php          ← Rejestracja konta
│   │   save.php              ← Zapis danych do JSON
│   │   edit_tabs.php         ← Operacje na zakładkach
│   │   log.php               ← Zapis dziennika działań
│   │   ...
│   ├───arch/                 ← Archiwum tematów
│   ├───trash/                ← Kosz (usunięte wpisy)
│   ├───css/                  ← Style CSS (np. add.css)
│   └───user/                 ← Konfiguracja kont użytkowników
│           config.json
│           configx.json
│
├───data/
│       dane.json             ← Główny plik danych
│
└───tools/                    ← Dodatkowe narzędzia (opcjonalnie)
```

---

## 🔧 Szybki start

1. Sklonuj repozytorium lub pobierz jako ZIP.
2. Umieść projekt na serwerze lokalnym (np. XAMPP, Laravel Valet, itp.).
3. Upewnij się, że foldery `data/`, `add_note/arch/`, `add_note/trash/` mają uprawnienia zapisu.
4. Otwórz w przeglądarce plik:
   ```
   /add_note/add.php
   ```
5. Gotowe! Możesz edytować dane z pliku `data/dane.json`.

---

## 🖼️ Wygląd i układ menu

Układ menu (zakładek) możesz ustawić w HTML, zmieniając klasę głównego kontenera:

```html
<div id="app" class="menu-horizontal">
```

Dostępne układy (obsługiwane przez CSS):

- `menu-horizontal` – menu zakładek u góry
- `menu-vertical-left` – menu pionowe po lewej stronie
- `menu-vertical-right` – menu pionowe po prawej stronie

### Przykład stylów:

```css
#app.menu-horizontal {
  flex-direction: column;
}

.menu-horizontal .top-bar {
  flex-direction: row;
  flex-wrap: wrap;
}
```

---

## ✏️ Opis działania

### Zakładki (`tabs`)
Zakładki są wczytywane z pliku `data/dane.json`, a ich etykiety (`label`) są automatycznie renderowane jako przyciski:

```html
<button
  v-for="tab in tabs"
  :key="tab.id"
  @click="selectTab(tab)"
  :class="{ active: selectedTab && selectedTab.id === tab.id }"
>
  {{ tab.label }}
</button>
```

### Treść zakładki (`items`)
Po kliknięciu zakładki, zawartość `items` danej zakładki jest wyświetlana dynamicznie w bloku `.content`.

---

## ✍️ Edytor danych (`add.php`)

Po uruchomieniu `add_note/add.php` otrzymujemy pełny edytor danych `dane.json`. Możliwości:

- dodawanie / edytowanie tematów
- modyfikowanie pól `title`, `description`, `tags`, `tresc`
- obsługa pól daty (`data_dodania`, `data_aktualizacji`)
- usuwanie tematów
- zarządzanie zakładkami (dodawanie, zmiana nazwy, usuwanie)
- autozapis (domyślnie co minutę)

---

## 🔧 Personalizacja wyświetlanych danych

Każdy element treści (np. temat, opis, tagi, data) możesz tymczasowo wyłączyć, stosując komentarz HTML (`<!-- -->`) w pliku `index.php`.

### Przykład — ukrycie opisu:

```html
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
```

Dzięki temu możesz łatwo sterować tym, co widzi użytkownik końcowy — bez usuwania kodu.

---

## 🔐 Tworzenie konta

Jeśli panel logowania jest aktywny, można utworzyć konto przy użyciu hasła **`rootPassword`**.  
Domyślne hasło to:

```
deko12
```

Po utworzeniu konta zaleca się zmianę tego hasła w pliku `add_note/user/configx.json`.

Można to zrobić za pomocą poniższego skryptu PHP:

```php
<?php
// Wygeneruj zaszyfrowane hasło do pliku configx.json
echo password_hash('TwojeNoweHasło', PASSWORD_DEFAULT);
?>
```

Skrypt ten możesz uruchomić lokalnie (np. otwierając w przeglądarce plik gen.php).  
Następnie wklej wynik w miejsce hasła w `configx.json`.

---

### 🧪 Panel debugowania

Edytor zawiera dodatkowy panel debugowania (`debug_panel.php`) z podglądem:

- logów (`debug.log`)
- historii działań
- zawartości kosza (`trash/`)
- archiwum (`arch/`)
- zarządzania kontami użytkowników

Jeśli nie chcesz korzystać z panelu debugowania, usuń lub zakomentuj tę linię z końca pliku `add.php`:

```php
<?php include 'debug_panel.php'; ?>
```

---

## 📬 Kontakt

**Autor:** Marcin Małysa  
**E-mail:** foczka344@gazeta.pl

Zgłoszenia błędów, sugestie rozwoju lub uwagi można przesyłać na podany adres e-mail.

---

## 📝 Uwagi końcowe

Projekt jest otwarty do dalszego rozwijania – może służyć jako:
- prosty edytor treści lokalnych
- zarządzanie strukturą danych JSON przez przeglądarkę
- baza dla aplikacji CMS lub systemu quizów/testów

---
