<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    /**
     * Số lượng mục mặc định trên mỗi trang cho từng khu vực
     */
    protected function getDefaultPerPage(string $area = 'frontend'): int
    {
        $perPageConfig = [
            'admin' => 10,
            'barber' => 10,
            'frontend' => [
                'services' => 4,
                'products' => 6,
                'news' => 6,
                'reviews' => 5,
                'default' => 6
            ]
        ];

        if ($area === 'frontend') {
            // Detect controller name based on current route
            $routeName = request()->route()->getName();
            $controller = explode('.', $routeName)[0] ?? 'default';
            
            return $perPageConfig[$area][$controller] ?? $perPageConfig[$area]['default'];
        }
        
        return $perPageConfig[$area] ?? $perPageConfig['frontend']['default'];
    }

    /**
     * Phân trang chung cho model
     *
     * @param Builder $query Query để phân trang
     * @param Request|null $request Request hiện tại (để giữ query string khi phân trang)
     * @param int|null $perPage Số lượng mục trên mỗi trang
     * @param string|null $area Khu vực (admin, barber, frontend)
     * @return LengthAwarePaginator
     */
    protected function paginateResults(
        Builder $query, 
        ?Request $request = null, 
        ?int $perPage = null, 
        ?string $area = null
    ): LengthAwarePaginator {
        $request = $request ?? request();
        $area = $area ?? $this->detectCurrentArea();
        $perPage = $perPage ?? $this->getDefaultPerPage($area);
        
        return $query->paginate($perPage)->withQueryString();
    }
    
    /**
     * Phát hiện khu vực hiện tại dựa trên route
     */
    protected function detectCurrentArea(): string
    {
        $routeName = request()->route()->getName();
        
        if (str_starts_with($routeName, 'admin.')) {
            return 'admin';
        } elseif (str_starts_with($routeName, 'barber.')) {
            return 'barber';
        }
        
        return 'frontend';
    }
    
    /**
     * Xác định tên view phân trang dựa trên khu vực
     */
    protected function getPaginationViewName(): string
    {
        $area = $this->detectCurrentArea();
        
        return match ($area) {
            'admin' => 'admin.partials.pagination',
            'barber' => 'barber.partials.pagination',
            default => 'frontend.partials.pagination',
        };
    }
} 