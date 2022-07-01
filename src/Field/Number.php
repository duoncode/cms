<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Request;

use  Conia\Value\Number  as  NumberValue;


class  Number  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): NumberValue
       {
           retu rn n ew NumberValue($req uest, $data);
     
 
}
}
