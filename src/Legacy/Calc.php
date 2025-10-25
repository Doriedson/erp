<?php

namespace App\Legacy;

class Calc extends Connection {

    protected function Calculate($query) {

        $this->query = "SELECT ($query) as total";

		parent::Execute();
    }

    public static function Sum($values) {

        $sum = new Calc();
        $query = "";

        foreach ($values as $value) {

            $query .= " + $value";
        }

        $sum->Calculate($query);

        $row = $sum->getResult();

        return $row['total'];
    }

    public static function Mod($dividend, $divisor) {

         if($divisor == 0) {

            return 0;
        }

        $mod = new Calc();
        $query = $dividend . " % " . $divisor;

        $mod->Calculate($query);

        $row = $mod->getResult();

        return $row['total'];
    }

    public static function Div($dividend, $divisor, $precision = 2) {

        if($divisor == 0) {

            return 0;
        }

        $quotient = new Calc();
        $query = "round($dividend / $divisor, $precision)";

        $quotient->Calculate($query);

        $row = $quotient->getResult();

        return $row['total'];
    }

    public static function Mult($value1, $value2, $precision = 2) {

        $product = new Calc();
        $query = "round($value1 * $value2, $precision)";

        $product->Calculate($query);

        $row = $product->getResult();

        return $row['total'];
    }
}