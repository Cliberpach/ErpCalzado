<?php

namespace App\Http\Services\Dashboard;

class DashboardManager
{
    private DashboardService $s_dashboard;

    public function __construct()
    {
        $this->s_dashboard  =   new DashboardService();
    }

    public function getData(array $data)
    {
        return $this->s_dashboard->getData($data);
    }

    public function getSales(array $data)
    {
        return $this->s_dashboard->getSales($data);
    }

    public function getSalesOrigin(array $data)
    {
        return $this->s_dashboard->getSalesOrigin($data);
    }

    public function getDataTopProducts(array $data)
    {
        return $this->s_dashboard->getDataTopProducts($data);
    }

    public function getConversionRate(array $data)
    {
        return $this->s_dashboard->getConversionRate($data);
    }

    public function getParesYearMonth(array $data)
    {
        return $this->s_dashboard->getParesYearMonth($data);
    }

    public function getSalesColor(array $data)
    {
        return $this->s_dashboard->getSalesColor($data);
    }

    public function getSalesSizes(array $data)
    {
        return $this->s_dashboard->getSalesSizes($data);
    }

    public function getCustomersActives(array $data)
    {
        return $this->s_dashboard->getCustomersActives($data);
    }

    public function getDeliveryTime(array $data)
    {
        return $this->s_dashboard->getDeliveryTime($data);
    }
}
