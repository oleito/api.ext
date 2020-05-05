<?php
/**
 *
 */
class filterData
{

    public function __construct()
    {}

    public function filtrar($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }
}
