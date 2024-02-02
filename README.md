# XmunBank
## Descrizione
XmunBank è un sistema bancario progettato per l'esame di sicurezza delle architetture orientate ai servizi. Si divide in due parti, una parte server sviluppata con [Laravel](https://laravel.com/) e una mobile web app client sviluppata con [Framework7](https://framework7.io/). Sulla parte server è ospitato il database e vengono fornite le APIs *(Application programming interfaces)*. 
### Obiettivo
L'obiettivo del progetto è quello di svillupare e dimostrare la sicurezza delle **APIs** per alcune tipologie di attacchi e vulnerabilità:
- **Injection Attacks:** Gli attacchi di injection avvengono quando dati non filtrati vengono inseriti in comandi o query, causando esecuzioni non autorizzate.
- **Cross-Site Request Forgery (CSRF):** Gli attacchi CSRF sfruttano la fiducia di un utente autenticato per eseguire azioni non volute a loro insaputa.
- **Insecure Direct Object References (IDOR):** Questa vulnerabilità si verifica quando un'applicazione fornisce accesso non autorizzato a oggetti o risorse direttamente tramite input utente.
  - *Casi particolari: BOLA, BFLA*
- **Rate Limiting Bypass:** Gli attaccanti cercano di bypassare i limiti imposti sul numero di richieste per prevenire attacchi a forza bruta.
- **Non-Validated Input:** L'assenza di una corretta convalida degli input può consentire agli attaccanti di inserire dati dannosi o manipolati.
- **Exposed Sensitive Data:** Esposizione non autorizzata di dati sensibili attraverso l'API.
- **Broken Authentication:** Problemi nell'implementazione dell'autenticazione, come password deboli o mancata gestione delle sessioni, possono portare a compromissioni della sicurezza.
- **No Access control via different URLs:** Utilizzare diversi endpoint per individuare quale non utilizza sistemi di autenticazione
- **Mass Assignment:** L'assegnazione di massa si verifica quando un consumatore API include nelle proprie richieste più parametri di quelli previsti dall'applicazione.
- **Business logic flaws:** Se un API non dispone di funzionalità di caricameno che non convalidano i payload codificati, l'attacante può caricare qualsiasi tipo di informazione codificata.

## Come installare ed eseguire
> [!IMPORTANT]
> **E' necessario aver installato i seguenti requisiti:**
> - APACHE
> - PHP >= 8.1 *con estensioni: **Ctype**, **cURL**, **DOM**, **Fileinfo**, **Filter**, **Hash**, **Mbstring**, **OpenSSL**, **PCRE**, **PDO**, **Session**, **Tokenizer**, **XML***
> - MySQL
> - Node.js e npm

1. Modificare il file **/Laravel/.env**, cambiare i valori di seguito indicati per far si che Laravel possa connettersi al database:
   ```
   DB_HOST=
   DB_PORT=
   DB_DATABASE=
   DB_USERNAME=
   DB_PASSWORD=
   ```

1. Per poter eseguire il server Laravel, posizionarsi nella directory e dal cmd eseguire:
   ```
   php artisan migrate
   php artisan serve
   ```

1. Per poter eseguire l'applicazione, posizionarsi nella directory e dal cmd eseguire:
   ```
   npm start
   ```

## Database
Questa è lo schema del database utilizzato:
![Schema database](/assets/sqlScheme.png)

## API Request
Laravel utilizza un sistema di routing in cui possono essere definiti gli url per accedere alle interfacce API **/Laravel/routes/api.php**, speicificando quali metodi sono ammessi. Per ogni richiesta effettuata, essa viene verificata dapprima se l'url corrsiponde ad un indirizzo definito nel router e successivamente se il metodo della richiesta è valido.
La richiesta passerà, dunque, attraverso un insieme di middelware, tra cui in particolare **CorsMiddleware**, **ThrottleRequestsMiddleware**, **VerifyCsrfTokenMiddleware**, **SanctumMiddleware**. Se i controlli effettuati nei Middleware vengono superati con successo la richiesta passa al controller di riferimento, dove è definita la logica della richiesta, tra cui la validazione degli input, le operazioni e gli output da mostrare.
<details>
<summary>Che cos'è un middleware in Laravel</summary>
In Laravel, un middleware è uno strato intermedio tra una richiesta HTTP in ingresso e il kernel dell'applicazione. Questo strato intermedio può eseguire azioni sulla richiesta in arrivo o sulla risposta in uscita, fornendo un meccanismo per modificare o interrompere il flusso di esecuzione dell'applicazione.
Un middleware può essere utilizzato per eseguire varie operazioni, come l'autenticazione, l'autorizzazione, la gestione della cache, il logging, la manipolazione della richiesta o della risposta, e altro ancora. Laravel fornisce un sistema di middleware potente e flessibile che consente agli sviluppatori di personalizzare il comportamento dell'applicazione in vari punti del ciclo di vita della richiesta.
</details>

Se le richieste vengono estinte con successo la risposta arriverà con un codice HTTP di successo 2XX, altrimenti la risposta con un messaggio di errore verrà restituita al client con un codice HTTP di errore 4XX.

### Schema riassuntivo richieste APIs
![Schema database](/assets/apiRequest.png)
