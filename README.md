# soteria


A port of https://github.com/voku/anti-xss with the ability for it to work on some older systems.

- This will be updated to just use Lars` code ;)

```PHP
use devtoolboxuk\soteria;
$this->security = new SoteriaService();
$this->security->xss_clean('Data');
```
