# Nette Ratchet extension

Implementace websocketového serveru Ratchet http://socketo.me, do Nette.


## Vlastnosti

- Implementuje potřebné komponenty Ratchet a většinu zašťituje v duchu Nete
- Zpřístupňuje služby kontajneru, a povoluje nastavení serveru pomocí konfiguračního souboru
- Sjednoduje služby, parametry - kontajner - mezi Nette aplikací a Ratchet servrem
- Komunikace zapouzdřená v duchu Presenteru, ale obsahuje vlastní Application - viz níže Application, Control
- Mapování client > server zpráv pomocí routování - viz níže Router
- Zprávy server > client je možné obsluhovat pomocí několika druhé response - viz níže Responses


## Instalace rozšíření

1. Stažení přes composer: **frosty22/ratchet**
2. Připojení DI rozšíření **Ale\Ratchet\DI\RatchetExtension**

> Pokud nevíte jakým způsobem připojit rošíření, dopoučuji použít rošíření https://github.com/vojtech-dobes/nette-extensions-list, které umožňuje následně v konfiguračním souboru definovat sekci "extensions", kde pak lze jednoduše přidat toto rošíření. Alternativně je nutné v boostrapu v události onCompile na Configuration zavěsit callback, který bude přidávat všechna Vaše rožšíření pomocí volání metody addExtension na Compiler.


## Responses

Zprávy ze serveru ke klientovi je možné zasílat několika způsoby - ve Vašich Controllerech, se nachází několik metod **send*()**. Ty slouží jako zkratky k zasílání Response, k daným klientům. Je možné napsat si vlastní response implementací interface **IResponse** a
rozšířením JS handleru těchto zpráv.

Response se liší minoritně jakým způsobem jsou na straně klienta v knihovně **jquery.ratchet.js** odchytávány a následně zprocesovány.


### MessageResponse

Toto je základní response, kterou je možné zaslat - je to čístá plaintext response a na straně klienta si ji musíte obsloužit sami, pokud používáte přiložený **jquery.ratchet.js**, příklad zde:

```php
class TestControl extends \Ale\Ratchet\UI\Control {

	public function handleSimple()
	{
		$this->send(new \Ale\Ratchet\Response\MessageResponse('pouze plain text'));
	}

}
```

```javascript
// Vytvoření spojení
var ws = $.websocket("ws://127.0.0.1:8080/", {
			message : function(data, event) {  // Přidání callbacku na všechny zprávy
				alert(data); // V příkladu vyhodí hlášku "pouze plain text"
			}
});

// Callback po vytvoření spojení se socket servrem
ws.bind("open", function(){

	// Odešleme zprávu na náš TestControl a handleSimple (bez parametrů)
    ws.send("Test:simple");

});

```


## CallResponse

Již komplexnější reponse, které se předává název callbacku, který se má u klienta zavolat na oblužném handler JS objektu, dále
se data přenáší ve formátu JSON a sami se převádí na straně PHP a JS.

```php
class TestControl extends \Ale\Ratchet\UI\Control {

	public function handleDefault($abc)
	{
		// $abc - bude obsahovat "test123" dle příkladu JS níže
		$this->send(new \Ale\Ratchet\Response\CallResponse('foo', array('bar' => 'baz')));
	}

}
```

```javascript
// Objekt obsahující naše oblužné callbacky (určeno pro CallResponse)
var sampleObject = new Object();

// Definujeme callback na property 'foo'
// bude zavolán dle příkladu výše v handleDefault, a vyhodí hlášku "baz"
sampleObject.foo = function(data) { alert(data.bar); };

// Vytvoření spojení
var ws = $.websocket("ws://127.0.0.1:8080/", {
			handler : sampleObject // Předáme handler našemu klientovi
});

// Callback po vytvoření spojení se socket servrem
ws.bind("open", function(){

	// Odešleme zprávu na náš TestControl a handleDefault
    ws.send("Test:default", { 'abc' : 'test123' });


});

```


## Application

## Router & Request

Slouží k převední přijaté zprávy na konkrétní **Request**, což je objekt, který obsahuje informace o tom, který controller, jeho metoda a s jakými parametry se má zavolat.

**Router** je objekt, který z přijaté zprávy z klienta vytvoří tento **Request** a předá ho **Application**, který obslouží tento proces - vytvoří příslušný controler, zavolá dané metody a předá parametry z **Requestu**.

**Router** musí obsahovat pouze jednu metodu **match**, která přijímá zprávu ve formátu string a vrací Request objekt.


### SimpleRouter

Výchozí router je SimpleRouter, který přijímá zprávy ve formátu JSON, a to s dvěma klíči **path** a **data**, kde path obsahuje cestu ke controlleru ve formátu jako je vytváření odkazů v nette presenterech tj. **Module:Controller:handle**, s tím že module je volitelný, a zároveň defaultní handle či controller je ve výchozím stavu pojmenován **default**.


## Server

## Control

## Connection
