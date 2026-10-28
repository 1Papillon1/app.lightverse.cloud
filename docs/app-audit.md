# App audit

Popis promjena napravljenih nakon pregleda aplikacije u odnosu na [zadatak](zadatak.md), s razlogom za svaku promjenu.

## 1. Uklanjanje Tailwind CSS-a iz konfiguracije

**Razlog:** aplikacija za dizajn koristi Bootstrap 5 i Bootstrap Icons preko CDN-a (`resources/views/layout/app.blade.php`). Tailwind je ostatak zadanog Laravel skeletona i nigdje se ne koristi. Nijedan Blade view ne učitava Vite assete (`@vite`).

**Gdje je Tailwind bio uključen:** `vite.config.js` ne spominje Tailwind izravno. Uključuje `resources/css/app.css`, a Tailwind se na taj CSS primjenjuje kroz PostCSS plugin u `postcss.config.js`.

**Promjena:** `postcss.config.js`

```diff
 export default {
     plugins: {
-        tailwindcss: {},
         autoprefixer: {},
     },
 };
```

**Nije mijenjano** (namjerno, mijenjana je samo konfiguracija):
- `vite.config.js`
- `tailwind.config.js`
- `resources/css/app.css` (još sadrži `@tailwind` direktive)
- `package.json` / `package-lock.json` (paket `tailwindcss` je i dalje u `devDependencies`)

Ako se kasnije bude radio `npm run build`, `@tailwind` direktive iz `app.css` ostat će neobrađene u izlaznom CSS-u. To ne utječe na aplikaciju jer se taj CSS ne učitava.

## 2. Dashboard s dvije kartice

**Razlog:** početna stranica (`/`) prikazivala je samo naslov "Dashboard".

**Promjena:**
- Novi `app/Http/Controllers/DashboardController.php`, ruta `/` sad ide preko njega (ime rute `dashboard`)
- `resources/views/dashboard.blade.php`:
  - **super admin:** kartice *All files* (ukupan broj datoteka) i *Users* (broj korisnika s ulogom `user`), s linkovima na admin stranice
  - **user:** kartice *My files* (broj vlastitih datoteka) i *Storage* (slobodan prostor "x / 3 GB free space left" s progress barom)

## 3. Auth

### 3.1 Registracija onemogućena s porukom

**Razlog:** zadatak ne traži registraciju jer korisnike kreira administrator (u ovoj aplikaciji seeder). Umjesto da `/register` vraća 404, korisnik dobiva jasnu poruku.

**Promjena:**
- `routes/web.php`: `GET /register` (samo za goste, ime `register`)
- `AuthController::showRegister()`
- `resources/views/auth/register.blade.php`: kartica "Registration is disabled" s gumbom natrag na login
- Login stranica ima link "Don't have an account? Register"
- Prijavljeni korisnik na `/register` biva preusmjeren na `/` (`guest` middleware)

### 3.2 Ograničenje pokušaja prijave (throttle)

**Razlog:** zaštita od brute-force napada na lozinke. Bez ograničenja moguće je slati neograničen broj pokušaja prijave.

**Promjena:** `routes/web.php`

```php
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
```

- najviše **5 zahtjeva u 1 minuti** po IP adresi
- broje se svi POST zahtjevi na `/login` (i uspješni i neuspješni)
- nakon toga Laravel vraća **429 Too Many Requests**, a prikazuje se `resources/views/errors/429.blade.php`
- uz rutu je komentar koji objašnjava zašto je ograničenje tu

### 3.3 Remember me

**Razlog:** korisnik može ostati prijavljen i nakon zatvaranja preglednika.

**Promjena:**
- Nova migracija `2026_10_27_120000_add_remember_token_to_users_table.php` dodaje kolonu `remember_token`. Tablica `users` je nije imala, a Laravel je treba za "remember me".
- `AuthController::login()`: `Auth::attempt($credentials, $request->boolean('remember'))`
- Login forma ima kvačicu "Remember me"

Migracija je pokrenuta na postojećoj bazi (`php artisan migrate`). Samo dodaje kolonu, podaci nisu dirani.

### 3.4 Prikaz grešaka na login formi

**Razlog:** polje lozinke nije prikazivalo validacijsku grešku.

**Promjena:** `resources/views/auth/login.blade.php`, polje `password` dobiva `is-invalid` i `@error('password')` poruku, isto kao `username`.

## 4. Error stranice

**Razlog:** zadane Laravel error stranice imaju vlastiti dizajn bez navigacije. Sad koriste layout aplikacije, pa korisnik ostaje u istom sučelju i ima gumb za povratak.

**Promjena:**
- `php artisan vendor:publish --tag=laravel-errors` objavio je predloške u `resources/views/errors/`
- `errors/minimal.blade.php` je prepravljen tako da nasljeđuje `layout.app` (Bootstrap kartica: kod, poruka, opis, gumbi *Back to home* i *Go back*)
- Zadržane i prilagođene stranice, svaka s opisom:

| Kod | Kada se pojavljuje u aplikaciji |
|---|---|
| 403 | user otvara `/admin/*` (RoleMiddleware) ili tuđu datoteku (FilePolicy) |
| 404 | nepostojeća stranica ili datoteka |
| 419 | istekla sesija / CSRF token (forma predugo otvorena) |
| 429 | previše pokušaja prijave (throttle) |
| 500 | greška na serveru |
| 503 | aplikacija u maintenance modu (`php artisan down`) |

- Obrisane neiskorištene stranice: `401`, `402` i `layout.blade.php`

**Napomena:** dok je `APP_DEBUG=true`, za neuhvaćene greške (500) Laravel prikazuje debug stranicu umjesto `500.blade.php`. Stranice 403, 404, 419, 429 i 503 prikazuju se uvijek.

## 5. Sitnice

### 5.1 Redni broj na admin popisu korisnika
**Razlog:** stupac `#` koristio je `$loop->iteration`, pa je na svakoj stranici paginacije kretao od 1.
**Promjena:** `resources/views/admin/users.blade.php` koristi `$users->firstItem() + $loop->index`, pa druga stranica kreće od 6.

### 5.2 Aktivni link u navigaciji
**Razlog:** u navbaru se nije vidjelo na kojoj se stranici korisnik nalazi.
**Promjena:** `resources/views/layout/app.blade.php`, svaki link dobiva Bootstrap klasu `active` preko `request()->is(...)`.

## 6. HTTP semantika

### 6.1 Ažuriranje profila preko PATCH
**Razlog:** ažuriranje postojećeg resursa semantički je `PATCH`, a ne `POST`.
**Promjena:** `routes/web.php` koristi `Route::patch('/profile', ...)`, a forma u `resources/views/user/index.blade.php` dobiva `@method('PATCH')`.

### 6.2 Poruka 403 iz RoleMiddlewarea
**Razlog:** middleware je vraćao 403 s porukom "Unauthorized". 401 Unauthorized znači da korisnik nije prijavljen, a 403 Forbidden da je prijavljen, ali nema pravo pristupa.
**Promjena:** `app/Http/Middleware/RoleMiddleware.php` koristi `abort(403, 'Forbidden')`, uz komentar.

## 7. Testovi

**Razlog:** projekt je imao samo Laravelove primjere testova.

**Promjena:**
- `phpunit.xml`: odkomentirani `DB_CONNECTION=sqlite` i `DB_DATABASE=:memory:`. Prije toga testovi bi radili na **pravoj MySQL bazi** iz `.env`. Sad rade na SQLite bazi u memoriji koja postoji samo dok testovi traju.
- `tests/Feature/AppAccessTest.php`, s `Storage::fake()` da se datoteke ne zapisuju na pravi disk:

| Test | Što provjerava |
|---|---|
| `test_user_cannot_access_admin_pages` | user na `/admin/files` i `/admin/users` dobiva 403 |
| `test_user_cannot_download_another_users_file` | user dobiva 403 na tuđu datoteku |
| `test_upload_over_storage_limit_is_rejected` | upload veći od preostalog prostora vraća grešku i ne sprema datoteku |
| `test_files_list_shows_five_files_per_page` | od 7 datoteka prva stranica ima 5, druga 2 |
| `test_login_is_throttled_after_five_attempts` | šesti pokušaj prijave u minuti vraća 429 |

Pokretanje:

```bash
php artisan test tests/Feature/AppAccessTest.php
```

Rezultat: 5 passed (15 assertions). Broj korisnika i datoteka u pravoj bazi isti je prije i poslije pokretanja.

**Napomena:** Laravelovi primjeri testova kasnije su obrisani i dodani su novi testovi (vidi sekciju 13).

## 8. Refaktor (bez promjene ponašanja)

### 8.1 Form Requests
**Razlog:** validacija je bila u kontrolerima. Laravel konvencija je izdvojiti je u Form Request klasu, pa kontroler ostaje kraći i bavi se samo logikom.
**Promjena:**
- `app/Http/Requests/StoreFileRequest.php`: pravila za upload (`required|file|max:10240`)
- `app/Http/Requests/UpdateProfileRequest.php`: pravila za profil (ime, prezime, adresa)
- `FileController::store(StoreFileRequest $request)` i `UserController::update(UpdateProfileRequest $request)`; profil se sprema s `$request->validated()`

### 8.2 Konstante za uloge
**Razlog:** nazivi uloga (`'super_admin'`, `'user'`) bili su ponavljani stringovi po cijelom kodu. Tipfeler u stringu ne javlja grešku, nego tiho ne radi.
**Promjena:** `Role::USER` i `Role::SUPER_ADMIN` u `app/Models/Role.php` koriste se u:
- `routes/web.php` (`'role:' . Role::SUPER_ADMIN`)
- `FilePolicy`
- `AdminController`, `DashboardController`
- `layout/app.blade.php`, `dashboard.blade.php`

Seederi nisu mijenjani.

### 8.3 Imenovane rute
**Razlog:** linkovi su bili hardkodirani (`url('files/' . $file->id . '/download')`, `href="/admin/files"`), a ime `files` bilo je dodijeljeno trima rutama, pa je vrijedilo samo zadnje. Profil se zvao `index`.
**Promjena:**

| Ruta | Staro ime | Novo ime |
|---|---|---|
| `GET /profile` | `index` | `profile` |
| `GET /files` | `files` | `files.index` |
| `POST /files` | `files` | `files.store` |
| `GET /files/{file}/download` | — | `files.download` |
| `DELETE /files/{file}` | `files` | `files.destroy` |

Svi linkovi, forme i redirecti (layout, dashboard, files, admin/files, error stranice, `AuthController`, `FileController`) koriste `route(...)`. Navbar označava aktivni link preko `request()->routeIs(...)`.

**Provjera:** testovi prolaze (5 passed). Uživo su provjerene sve stranice za admina i za usera, linkovi za download i brisanje, akcija upload forme, validacija profila kroz Form Request i logout.

## 9. Upload i brisanje: obrada grešaka i logiranje

**Razlog:** upload je prvo spremao datoteku na disk, pa tek onda zapis u bazu. Ako zapis pukne, datoteka ostaje na disku bez zapisa. Kod brisanja je bilo obrnuto. Ništa se nije logiralo.

**Promjena:** `app/Http/Controllers/FileController.php`
- **Upload:**
  - ako spremanje na disk ne uspije, korisnik dobiva poruku "Upload failed", a greška se logira
  - ako zapis u bazu ne uspije (`try/catch`), već spremljena datoteka briše se s diska, a greška se logira
  - uspješan upload logira se kao `File uploaded` (id datoteke, id korisnika)
- **Brisanje:**
  - prvo se briše zapis iz baze (`try/catch`); ako to ne uspije, datoteka ostaje netaknuta, korisnik dobiva poruku, a greška se logira
  - ako datoteka nakon toga ne postoji na disku, logira se upozorenje (warning)
  - uspješno brisanje logira se kao `File deleted` (id datoteke, vlasnik, tko je obrisao); vidi se i kad super admin briše tuđu datoteku
- `files/index.blade.php` i `admin/files.blade.php` prikazuju `session('error')` kao crveni alert

Logovi su u `storage/logs/laravel.log`.

## 10. Dopušteni tipovi datoteka

**Razlog:** upload je prihvaćao bilo koju datoteku (npr. `.exe`, `.php`).

**Promjena:** `StoreFileRequest` ima `mimes:` whitelist, isti popis tipova za koje `File` model ima ikonu:
`pdf, doc, docx, xls, xlsx, csv, ppt, pptx, jpg, jpeg, png, gif, svg, webp, zip, rar, 7z, txt, mp3, wav, mp4, mov, avi`

`mimes` provjerava sadržaj datoteke, a ne samo ekstenziju. Upload forma ispod polja ispisuje dopuštene tipove.

## 11. Super admin na popisu korisnika

**Razlog:** seedani super admin ima i ulogu `user`, pa se pojavljuje na admin popisu korisnika, što može zbuniti.

**Odluka:** ostaje na popisu jer zadatak traži "sve korisnike koji imaju ulogu usera", a on je ima (i može uploadati datoteke). Dodan je stupac **Roles** s badgeovima (`super_admin` je tamni badge), pa se odmah vidi tko je tko. Uloge se učitavaju s `with('roles')`, bez dodatnog upita po retku. Odluka je objašnjena i u komentaru u `AdminController::users()`.

## 12. Higijena koda

**Razlog:** u kodu su bili ostaci zadanog Laravel skeletona (npr. `email_verified_at` cast bez te kolone), nekorišteni importi i varijable, nepotrebni traitovi i neujednačeno uvlačenje. Recenzent to vidi na prvi pogled, a ništa od toga ne radi ništa.

**Promjena:**
- `File` model: maknut nepotreban `Notifiable` trait i import modela iz istog namespacea
- `User` model: maknut `email_verified_at` cast (nema te kolone), zakomentirani `MustVerifyEmail` import i importi iz istog namespacea
- `UserController`: maknuta nekorištena varijabla `$username`, `Auth::user()` zamijenjen s `auth()->user()` (isto kao u ostalim kontrolerima)
- `FilePolicy`: maknut zastarjeli docblock "Create a new policy instance."
- **Laravel Pint** (`./vendor/bin/pint app routes tests`): ujednačeno formatiranje, uvlačenje, redoslijed importa i maknuti nekorišteni importi (npr. `Request` u `AdminController`)

**Namjerno nije dirano:** `database/` (seederi, factory, migracije). Tablica `password_reset_tokens` u migraciji i nekorišteni importi u `UserFactory` ostaju. Migracija je već pokrenuta, a mijenjanje pokrenutih migracija je loša praksa.

**Provjera:**
- `pint --test`: čisto
- testovi: 5 passed
- uživo: `.exe` i `.php` odbijeni s porukom o dozvoljenim tipovima; `.txt` uploadan pa obrisan, a oba događaja zapisana u logu; stupac Roles prikazuje `super_admin` badge

## 13. Dodatni testovi i uklanjanje primjera

**Razlog:** prvih 5 testova nije pokrivalo stvari dodane kasnije (registracija, remember me, whitelist tipova, logiranje, brisanje, PATCH profila). Laravelovi primjeri testova nisu testirali ništa iz aplikacije, a `tests/Feature/ExampleTest.php` je padao (očekivao je 200 na `/`, a dobivao 302).

**Promjena:**
- Obrisani `tests/Feature/ExampleTest.php` i `tests/Unit/ExampleTest.php`. U `tests/Unit` dodan je `.gitkeep`, jer `phpunit.xml` očekuje taj folder, a git ne sprema prazne foldere.
- Novi `tests/Feature/AppFeaturesTest.php` (isto: SQLite u memoriji, `Storage::fake()`):

| Test | Što provjerava |
|---|---|
| `test_register_page_shows_disabled_message` | `/register` prikazuje poruku da je registracija onemogućena |
| `test_login_with_remember_me_sets_remember_token` | prijava s "remember me" preusmjerava na dashboard i sprema `remember_token` |
| `test_upload_of_disallowed_file_type_is_rejected` | `.exe` se odbija, ništa se ne sprema |
| `test_upload_of_allowed_file_is_stored_and_logged` | `.pdf` je u bazi i u storageu, a u log je zapisano `File uploaded` |
| `test_owner_can_delete_own_file` | vlasnik briše datoteku, nestaje i zapis i datoteka na disku |
| `test_super_admin_can_delete_another_users_file` | super admin briše tuđu datoteku |
| `test_profile_is_updated_with_patch_and_validated` | PATCH sprema profil, prazno ime vraća grešku, stari POST vraća 405 |

**Pokretanje:** `php artisan test`

**Rezultat:** 12 passed (40 assertions). Broj korisnika i datoteka u pravoj bazi isti je prije i poslije pokretanja.

## 14. Sučelje: potvrda brisanja i navbar na mobitelu

### 14.1 Potvrda brisanja preko Bootstrap modala
**Razlog:** brisanje se potvrđivalo browserovim `confirm()` prozorom, koji ne izgleda kao ostatak aplikacije. Zadatak traži Bootstrap.
**Promjena:**
- Novi partial `resources/views/files/delete-modal.blade.php`: jedan modal s formom (`@csrf`, `@method('DELETE')`) i kratkom skriptom. Prije otvaranja modala skripta upisuje URL za brisanje i ime datoteke iz kliknutog gumba (`data-action`, `data-name`).
- `files/index.blade.php` i `admin/files.blade.php`: gumb *Delete* otvara modal umjesto da šalje formu, a partial se uključuje s `@include('files.delete-modal')`.
- Brisanje na serveru nije mijenjano (ista ruta `files.destroy`, isti policy).

### 14.2 Navbar na mobitelu
**Razlog:** `navbar-expand` se nikad ne skuplja, pa su se na uskom ekranu linkovi, korisničko ime i Logout gurali u jedan red.
**Promjena:** `resources/views/layout/app.blade.php`
- `navbar-expand-md`: ispod 768 px linkovi se skupljaju u hamburger izbornik (`navbar-toggler` + `collapse`)
- korisničko ime i Logout su u `d-flex` bloku s razmakom; uklonjen je inline `style="margin-right"`
- gost (login stranica) ne vidi hamburger jer nema linkova

**Provjera:**
- testovi: 12 passed
- `/files` i `/admin/files` imaju po jedan modal, svaki redak ima gumb s ispravnim `data-action`, a `confirm()` više nema
- hamburger postoji za prijavljenog korisnika, a za gosta ne
- otvaranje modala i skupljanje izbornika ovise o JavaScriptu u browseru, pa ih treba kliknuti ručno

## 15. Tablice na mobitelu

**Razlog:** na mobitelu (npr. iPhone, 393 px) tablice su bile šire od ekrana. Cijela stranica se zato proširila (navbar je ostao uži od sadržaja), dugačka imena datoteka lomila su se u više redova, a gumbi Download i Delete padali su jedan ispod drugog.

**Promjena:** `files/index.blade.php`, `admin/files.blade.php`, `admin/users.blade.php`
- tablice su u `table-responsive`, pa se tablica po potrebi pomiče unutar sebe, a stranica ostaje široka kao ekran
- ispod `md` (768 px) manje bitni stupci se skrivaju (`d-none d-md-table-cell`), a njihovi podaci prikazuju se sitno ispod imena (`d-md-none`):
  - **Files:** sakriveni *Added* i *Size*, ispod imena "datum · veličina"
  - **All files:** sakriveni *Added By*, *Added* i *Size*, ispod imena "Added by Ime Prezime · veličina" (podatak *Added By* ostaje vidljiv)
  - **Users:** sakriveni *#*, *Name*, *Used* i *Limit*; ime ispod usernamea, a *Roles* i *Free space* ostaju
- gumbi Download i Delete na mobitelu su samo ikone (`bi-download`, `bi-trash`) s `title` i `aria-label`, a na desktopu ikona + tekst; `text-nowrap` ih drži u istom redu
- dugačka imena lome se unutar ćelije (`text-break`)

**Provjera:** testovi 12 passed; sve tri stranice vraćaju 200 i sadrže novi markup. Izgled na mobitelu treba provjeriti u browseru (DevTools, iPhone).

## 16. Autorizacija na razini rute (samo napomena)

Pravo na download i brisanje provjerava `FilePolicy` preko `Gate::authorize()` u `FileController`-u. To ostaje tako. Ista provjera mogla bi se napraviti i na razini rute (`->can('download', 'file')`, `->can('delete', 'file')`). Kod nije mijenjan.

Komentari u `routes/web.php` kasnije su skraćeni: ostali su samo kratki komentari uz throttle, registraciju i rute za download i brisanje (zašto su izvan `role:user` grupe, plus jedna linija o alternativi s `->can()`). Detaljna objašnjenja su u ovom dokumentu (sekcije 3 i 17).

## 17. Zaštita ruta: `role:user` i `whereNumber`

### 17.1 `role:user` na profilu, popisu datoteka i uploadu
**Razlog:** rute `/profile`, `/files` (GET) i `/files` (POST) tražile su samo prijavu (`auth`). Korisnik koji ima **samo** ulogu `super_admin` (bez `user`) u navbaru ne vidi linkove Files i Profile, ali je ručnim upisom URL-a mogao otvoriti te stranice i uploadati datoteke. Prema zadatku (točka 2b) profil, vlastite datoteke i upload pripadaju ulozi user. Ovo je bilo jedino mjesto gdje pravila pristupa nisu do kraja pratila zadatak. Seedani super admin ima obje uloge, pa se problem u praksi nije pojavljivao.

**Promjena:** `routes/web.php`
- `profile`, `profile.update`, `files.index` i `files.store` premješteni su u grupu `Route::middleware('role:'.Role::USER)`
- `files.download` i `files.destroy` su **namjerno izvan** te grupe: koristi ih i super admin sa stranice All Files. Pravo na njih i dalje provjerava `FilePolicy` (vlasnik ili super admin).

### 17.2 `whereNumber('file')` na rutama s `{file}`
**Razlog:** `{file}` je id datoteke (broj). Bez ograničenja `/files/abc/download` ide do route model bindinga, koji radi upit u bazu i tek onda vraća 404.

**Promjena:** `files.download` i `files.destroy` imaju `->whereNumber('file')`. Ruta s nebrojčanim id-em uopće ne odgovara, pa Laravel vraća 404 bez upita u bazu. Za korisnika je ponašanje isto (404).

### 17.3 Testovi
U `tests/Feature/AppAccessTest.php` dodana su dva testa:

| Test | Što provjerava |
|---|---|
| `test_super_admin_without_user_role_cannot_access_user_pages` | korisnik samo s ulogom `super_admin` dobiva 403 na `/files` i `/profile`, ali i dalje može downloadati tuđu datoteku |
| `test_non_numeric_file_id_returns_404` | `/files/abc/download` (GET) i `/files/abc` (DELETE) vraćaju 404 |

**Provjera:**
- `php artisan test`: 14 passed (45 assertions)
- `php artisan route:list`: `files.index` i `files.store` imaju `role:user`, a `files.download` i `files.destroy` nemaju
- uživo kao `demo_admin` (ima obje uloge): `/files`, `/profile` i `/admin/files` vraćaju 200, `/files/abc/download` 404, a download tuđe datoteke 200

## 18. Provjera

Svaka sekcija iznad ima vlastitu provjeru. Ovdje je sažetak.

**Automatski testovi:** `php artisan test`: 14 passed (45 assertions), na SQLite bazi u memoriji (sekcije 7, 13 i 17.3).

**Formatiranje:** `./vendor/bin/pint --test app routes tests`: čisto (sekcija 12).

**Ručna provjera** na https://app.lightverse.cloud nakon zahvata iz sekcija 2 do 5:

| Provjera | Rezultat |
|---|---|
| `/register` za gosta: poruka, za prijavljenog: redirect na `/` | ✅ |
| Link na registraciju na login stranici | ✅ |
| Prijava s "remember me" postavlja `remember_web_*` cookie i `remember_token` u bazi | ✅ |
| Greška na polju lozinke | ✅ |
| Throttle: nakon 5 pokušaja u minuti 429 s prilagođenom stranicom | ✅ |
| 404, 419, 403 s prilagođenim stranicama | ✅ |
| Dashboard za super admina i za usera | ✅ |
| Admin Users, druga stranica kreće od #6 | ✅ |
| Aktivni link u navbaru | ✅ |

Ručne provjere za kasnije zahvate (sekcije 6 do 17) opisane su na kraju svake od tih sekcija.

**Nije provjereno u browseru:** otvaranje modala za brisanje, hamburger izbornik i izgled tablica na mobitelu (sekcije 14 i 15). Ovise o JavaScriptu i CSS-u u browseru, a provjeren je samo HTML koji server vraća.
