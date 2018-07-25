# Core

## BaseEntity, BaseRepository, BasePresenter,..
Core modul obsahuje zakladne triedy od ktorych by mali dedit vsetky objekty tychto typov v systeme

## Register
Zjednoteny sposob na registrovanie typov do pluginov. Register ma jeden parameter ktory je nazov triedy ktore musia pridavane typy implementovat. Novy register sa vytvori takto.
```
class FilterHandlersRegister extends \Wame\Core\Registers\BaseRegister 
{
    public function __construct() 
    {
        parent::__construct(\Nazov\Triedy\Ktore\Register\Berie::class);
    }
}
```

## Control "Status"
Tento stav maju presentre a vsetky komponenty. Zabezpecuju predavanie stavu medzi komponentami/presentrom. Premenne v stave sa propaguju smerom dole do deti komponenty.

Na citanie hodnoty sa da prihlasit tymto sposobom:
```
$this->status->get("def", function($value) {
    //callback volany pri zmene hodnoty
});
```

Alebo bez eventu, ale nieje zarucene ze hodnota je uz vyplnena. Vrati len aktualnu hodnotu.
```
$this->status->get("def"), "return");
```