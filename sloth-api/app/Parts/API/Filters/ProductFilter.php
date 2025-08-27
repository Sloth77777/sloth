<?php

declare(strict_types=1);

namespace App\Parts\API\Filters;

use App\Enum\ProductFilterEnum;
use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{
    protected function getCallbacks(): array
    {
        return [
            ProductFilterEnum::PRICES->value => [$this, 'prices'],
            ProductFilterEnum::PRICE_MIN->value => [$this, 'priceMin'],
            ProductFilterEnum::PRICE_MAX->value => [$this, 'priceMax'],
            ProductFilterEnum::CATEGORIES->value => [$this, 'categories'],
            ProductFilterEnum::SEARCH->value => [$this, 'search'],
        ];
    }


    public function search(Builder $builder, $value): void
    {
        $builder->where('title', 'ilike', "%{$value}%");
    }

    public function categories(Builder $builder, $values): void
    {
        $builder->whereIn('category_id', (array)$values);
    }

    public function priceMin(Builder $builder, $value): void
    {
        $builder->where('price', '>=', $value);
    }

    public function priceMax(Builder $builder, $value): void
    {
        $builder->where('price', '<=', $value);
    }

    public function prices(Builder $builder, $value): void
    {
        if (is_array($value)) {
            if (isset($value['min']) && $value['min'] !== '') {
                $this->priceMin($builder, $value['min']);
            }
            if (isset($value['max']) && $value['max'] !== '') {
                $this->priceMax($builder, $value['max']);
            }
        }
    }

}
