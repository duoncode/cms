<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;

use  Conia\Value\Time  as  TimeValue;


class  Time  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): TimeValue
       {
           retu rn n ew TimeValue($req uest, $data);
     
 
}
}
