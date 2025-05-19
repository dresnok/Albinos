# Edytor danych JSON z zakładkami (Vue 3)

Prosty edytor danych JSON oparty o Vue 3 wraz z przykładami zastosowania. Kod pozwala w łatwy sposób modyfikować plik `dane.json` (dodawanie, edytowanie, usuwanie tematów), a interfejs użytkownika można elastycznie dostosować do preferowanego układu menu (poziomego lub pionowego) – jedynie przez zmianę klas CSS.

---

## 🌐 Wersja demonstracyjna

Przykład filtracji danych:  
[http://asperion24.eu/github/albinos/1.13.1a-3/](http://asperion24.eu/github/albinos/1.13.1a-3/)  
Panel edycji json:  
[http://asperion24.eu/github/albinos/1.13.1a-3/add_note/add.php](http://asperion24.eu/github/albinos/1.13.1a-3/add_note/add.php)

---

## 📁 Struktura projektu

```
│   debug.log
│   index.php
│   log.php
│
├───add_note
│   │   add.php
│   │   archi.php
│   │   auth.php
│   │   backup_data.php
│   │   debug.log
│   │   debug_panel.php
│   │   debug_vue_app.js
│   │   edit_tabs.php
│   │   gen.php
│   │   log.php
│   │   log.txt
│   │   login.php
│   │   README.md
│   │   register.php
│   │   save.php
│   │   upload_images.php
│   │
│   ├───arch
│   ├───css
│   │       add.css
│   │       debug_log_panel.css
│   │
│   ├───src
│   │       icons.js
│   │
│   ├───trash
│   └───user
│           config.json
│           configx.json
│
├───data
│       dane.json
│
└───img
    └───all_images
            2024-11-05_083955829_Storrada Lub_SkillUp.png
            2024-11-05_084050548_Storrada Lub_LevelUp.png
            2024-11-05_084109496_Storrada Lub_SkillUp.png
            2024-11-05_110047830_Storrada Lub_LevelUp.png
            2024-11-05_110331149_Storrada Lub_SkillUp.png
            2024-11-05_111155592_Storrada Lub_LevelUp.png
            2024-11-05_111532806_Storrada Lub_SkillUp.png
            2024-11-05_111654584_Storrada Lub_SkillUp.png
            2024-11-06_010653235_Storrada Lub_LevelUp.png
            2024-11-06_011524629_Storrada Lub_SkillUp.png

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

## Dostęp do json
Plik z danymi znajduje się pod ścieżką: data/dane.json

```
{
  "id": "zakladka-01",
  "label": "Ekspowiska",
  "items": [
    {
      "id": "item-001",
      "title": "Łatwo dostępne expowisko",
      "description": "Fog Fury Forest",
      "tags": ["carlin", "expowisko"],
      "tresc": "<p>Opis miejsca expienia...</p>",
      "data_dodania": "2025-05-15",
      "data_aktualizacji": "2025-05-19",
      "ss": "tak",
      "ważne": "tak"
    }
  ]
}
```

Możesz tworzyć wiele zakładek, a w każdej z nich wiele tematów. Każdy temat może zawierać dodatkowe etykiety (np. "ss": "tak", "ważne": "tak"), które są oznaczeniami użytkownika – używane np. do filtrowania ważnych lub specjalnych wpisów.

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
     <ul>
        <li v-for="tag in uniqueTags" :key="'tag-' + tag" @click="selectTag(tag)">
          {{ tag }}
        </li>
      </ul>
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
