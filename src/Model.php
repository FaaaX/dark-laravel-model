<?php namespace DarkTemplar\DarkModel;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->addCasts();
    }

    private function addCasts() {
        $cache_key = $this->getTable().'_casts';

        if(!\Cache::has($cache_key)) {
            $schema = \DB::getDoctrineSchemaManager();
            $tables = $schema->listTables($this->getTable());
            foreach ($tables as $table) {
                foreach ($table->getColumns() as $column) {
                    $this->casts[$column->getName()] = $column->getType()->getName();
                }
            }
            \Cache::forever($this->getTable() . '_casts', $this->casts);
        } else
            $this->casts = \Cache::get($cache_key);
    }
    public function getCreatedAtAttribute($value) {
        return Carbon::createFromTimestamp(strtotime($value))
            ->timezone('Europe/Copenhagen') // TODO  hent fra session eller db
            ->toDateTimeString();
    }
}
