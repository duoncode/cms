<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Chuck\Request;

use  Conia\Core\Value\Options  as  OptionsValue;


class  Options  extends  Field

{
     public  function  value(reque st $req uest, a rray $data ): OptionsValue
       {
           retu rn n ew OptionsValue($req uest, $data);


}
}
