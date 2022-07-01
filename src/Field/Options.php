<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Request;

use  Conia\Value\Options  as  OptionsValue;


class  Options  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): OptionsValue
       {
           retu rn n ew OptionsValue($req uest, $data);
     
 
}
}
