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

## 🔐 Dostęp i debugowanie

Edytor posiada opcjonalny **panel debugowania** z podglądem logów, historii, kosza i archiwum:

- plik: `add_note/debug_panel.php`
- logi: `add_note/debug.log`
- konfiguracja użytkownika: `add_note/user/config.json`

### Tworzenie konta
Jeśli panel logowania jest aktywny, można utworzyć konto przy użyciu hasła `root`. Domyślne hasło to:

```
temp123
```

Po utworzeniu konta zaleca się zmianę hasła "root" w `add_note/user/configx.json`.
Można to zrobić za pomocą poniższego skryptu PHP:

```php
<?php
// Wygeneruj zaszyfrowane hasło do pliku configx.json
echo password_hash('TwojeNoweHasło', PASSWORD_DEFAULT);
?>
```

Skrypt ten należy uruchomić lokalnie (np. w localhost/gen.php) i wkleić wynik jako wartość pola password w configx.json.
Jeśli nie chcesz korzystać z panelu debugowania, usuń lub zakomentuj tę linię z końca `add.php`:

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
