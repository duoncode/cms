<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;

use  Conia\Value\Html;


class  Iframe  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): Html
       {
           retu rn n ew Html($req uest, $data);
     
 
}
}
