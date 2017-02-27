<?php

namespace config;

class Validation
{
    //ajouter validation alnum + ponctuation + espaces
    public function validateInt($val)
    {
        $val = filter_var($val, FILTER_VALIDATE_INT);
        return ($val !== FALSE);
    }

    public function validateBoolean($val)
    {
        $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        return ($val !== FALSE);
    }

    public function validateFloat($val)
    {
        $val = filter_var($val, FILTER_VALIDATE_FLOAT);
        return ($val !== FALSE);
    }

    public function validateAlnum($val)
    {
        $filter="#^[a-zA-Z0-9]*$#";
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validateAlpha($val)
    {
        $filter="#^[a-zA-Z]*$#";
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validateAlnumLongueur($val,$longueur)
    {
        if($this->validateInt($longueur)) {
            $filter = "#^[a-zA-Z0-9]{".$longueur."}.$#";
        }
        else{
            return false;
        }
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validateAlphaLongueur($val,$longueur)
    {
        if($this->validateInt($longueur)) {
            $filter = "#^[a-zA-Z]{".$longueur."}.$#";
        }
        else{
            return false;
        }
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validateEntierIntervalleInclus($val,$min,$max)
    {
        if($this->validateInt($min) && $this->validateInt($max) && $this->validateInt($val)){
            if($val>=$min && $val<=$max)
                return true;
            else
                return false;
        }
        else{
            return false;
        }
    }

    public function validateReelIntervalle($val,$min,$max)
    {
        if($this->validateFloat($min) && $this->validateFloat($max)){
            $filter="#^[".$min."-".$max."].$#";
        }
        else{
            return false;
        }
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validateStringDansTab($val,$tab)
    {
        if($tab==null){
            return false;
        }
        return in_array($val,$tab);
        /*
        $filter='#^(';
        foreach($tab as $ligne){
            if($ligne==$tab[count($tab)-1]){
                $filter=$filter.$ligne;
            }
            else {
                $filter = $filter . $ligne . '|';
            }
        }
        $filter=$filter.'){1,}$#';
        $val = preg_match($filter, $val);
        return $val;
        */
    }

    public function validateMorceauStringDansTab($val,$tab)
    {
        if($tab==null){
            return false;
        }

        $filter='#(';
        foreach($tab as $ligne){
            if($ligne==$tab[count($tab)-1]){
                $filter=$filter.$ligne;
            }
            else {
                $filter = $filter . $ligne . '|';
            }
        }
        $filter=$filter.'){1,}#';
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validatePrintable($val)
    {
        $filter="#^[\x20-\x7E]*$#";
        $val = preg_match($filter, $val);
        return $val;
    }

    public function validatePrintableSansEspaces($val)
    {
        $filter="#^[\x21-\x7E]*$#";
        $val = preg_match($filter, $val);
        return $val;
    }

    public function nettoyerString($val){
        $out=filter_var($val,FILTER_SANITIZE_STRING);
        if($out)
            return $out;
        return "";
    }
    //http://www.regular-expressions.info/posixbrackets.html
    //méthode inArray
}