# Zadatak

## Opis aplikacije

Aplikacija je primjer "cloud" aplikacije gdje je moguće uploadati nove datoteke te downloadati i obrisati postojeće. Za dizajn se poslužite Bootstrapom. Aplikacija ne treba izgledati isto kao ona sa slike, ali treba zadovoljavati sljedeće korisničke zahtjeve:

1. **Korisnik** se mora moći ulogirati u aplikaciju pomoću korisničkog imena i lozinke.
2. Korisnici mogu imati jednu ili više uloga (npr. super administrator, administrator, user).
   - **a) Prijava kao super administrator**
     Ukoliko je trenutni korisnik u ulozi super administratora, njegove ovlasti su:
     - pregled **SVIH** datoteka uploadanih na cloud, downloadanje te brisanje istih (uz podatke u tablici treba imati dodatni stupac **Added By** koji govori koji user je uploadao koji dokument)
     - pregled **SVIH** korisnika koji imaju ulogu usera i koliko je svakom korisniku ostalo prostora za pohranu
   - **b) Prijava kao user**
     Svaki user ima određeni kapacitet koji može uploadati na cloud (npr. 3GB).
     Ukoliko je trenutni korisnik u ulozi usera, njegove ovlasti su:
     - pregled datoteka koje je on uploadao na cloud, downloadanje te brisanje istih
     - ažuriranje vlastitih podataka na profilu (ime, prezime, adresa, …). Također na profilu treba biti prikazan izračun koliko je korisniku preostalo slobodnog mjesta za pohranu (npr. 2/3GB free space left).
3. Ukoliko je broj uploadanih datoteka veći od 5 potrebno je napraviti paginaciju i prikazivati 5 uploadanih datoteka po stranici.
4. Ikona uploadanog dokumenta treba se mijenjati s obzirom na ekstenziju dokumenta (pogledati sliku).

## Očekivana tehnička implementacija aplikacije

- Tip projekta: Laravel Web App
- Ciljani framework: Laravel 11
- Ciljana baza podataka: MySQL

Implementacija kompletne aplikacije treba sadržavati:

1. Database Migrations
2. Model Factories
3. Database Seeding
4. Blade Templates
5. Implementacija vlastitog middleware-a koji ograničava pristup stranicama prema ulozi korisnika

---

## Checklist zahtjeva

### Funkcionalni zahtjevi

- [ ] Login pomoću **korisničkog imena** (username, ne email) i lozinke
- [ ] Logout
- [ ] Korisnik može imati **više uloga** (many-to-many User ↔ Role)
- [ ] Uloge: super administrator, administrator, user
- [ ] Upload datoteka (provjera da ne prelazi preostali kapacitet korisnika)
- [ ] Download datoteka
- [ ] Brisanje datoteka (i iz storagea, ne samo iz baze)

**Super administrator**
- [ ] Popis **svih** datoteka sa stupcem **Added By**
- [ ] Download i brisanje bilo koje datoteke
- [ ] Popis svih korisnika s ulogom *user* i njihov **preostali prostor**

**User**
- [ ] Popis samo **vlastitih** datoteka
- [ ] Download i brisanje samo vlastitih datoteka (zaštita od pristupa tuđim datotekama)
- [ ] Svaki user ima kapacitet pohrane (npr. 3GB)
- [ ] Profil: uređivanje vlastitih podataka (ime, prezime, adresa, …)
- [ ] Profil: prikaz preostalog prostora (npr. „2/3GB free space left")

**Općenito**
- [ ] Paginacija – 5 datoteka po stranici
- [ ] Ikona datoteke ovisno o ekstenziji (pdf, doc/docx, xls/xlsx, slike, zip, txt, …; default ikona za ostalo)
- [ ] Dizajn pomoću Bootstrapa

### Tehnički zahtjevi

- [ ] Laravel 11
- [ ] MySQL baza
- [ ] Migracije (users, roles, role_user pivot, files)
- [ ] Model factories (User, File, …)
- [ ] Seederi (uloge, super admin, testni useri, eventualno datoteke)
- [ ] Blade predlošci (layout + view-ovi)
- [ ] **Vlastiti** middleware za ograničavanje pristupa prema ulozi
