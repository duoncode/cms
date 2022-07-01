<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;

use  Conia\Value\Files;


class  File  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): Files
       {
           retu rn n ew Files($req uest, $data);
     
 
}
}
