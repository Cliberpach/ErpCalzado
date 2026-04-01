<?php

namespace App\Http\Services\Dashboard;

class DashboardService
{
    private DashboardRepository $s_repository;

    public function __construct()
    {
        $this->s_repository =   new DashboardRepository();
    }

    public function getData(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];

        $dispatchs          =   $this->s_repository->dispatchs();
        $carousel           =   $this->s_repository->getDataCarousel($year, $month, $sede);

        return (object)[
            'dispatchs'         =>  $dispatchs,
            'carousel'          =>  $carousel
        ];
    }

    public function getSales(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];

        $sales_year         =   $this->s_repository->salesYear($year, $sede);
        return $sales_year;
    }

    public function getSalesOrigin(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $tipo   =   $data['tipo'];

        $sales_year         =   $this->s_repository->salesOrigin($year, $month, $sede, $tipo);
        return $sales_year;
    }

    public function getDataTopProducts(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $color  =   $data['color'];
        $talla  =   $data['talla'];

        $top_products   =   $this->s_repository->topProducts($year, $month, $sede, $color, $talla);
        return $top_products;
    }

    public function getConversionRate(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];

        $conversion_rate    =   $this->s_repository->conversionRate($year, $sede);
        return $conversion_rate;
    }

    public function getParesYearMonth(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $tipo   =   $data['tipo'];
        $data   =   $this->s_repository->getParesYearMonth($tipo, $year, $month, $sede);
        return $data;
    }

    public function getSalesColor(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $data   =   $this->s_repository->getSalesColor($year, $month, $sede);
        return $data;
    }

    public function getSalesSizes(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $data   =   $this->s_repository->getSalesSizes($year, $month, $sede);
        return $data;
    }

    public function getCustomersActives(array $data)
    {
        $customers          =   $this->s_repository->customers();
        return $customers;
    }

    public function getDeliveryTime(array $data)
    {
        $year   =   $data['year'];
        $sede   =   $data['sede'];
        $month  =   $data['month'];
        $delivery_time   =   $this->s_repository->getDeliveryTime($year, $month, $sede);
        return $delivery_time;
    }
}
