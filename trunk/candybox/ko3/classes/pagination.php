<?php defined('SYSPATH') or die('No direct script access.');

class Pagination extends Kohana_Pagination {
    
    function set_current_page($page)
    {
        $this->current_page = $page;
        // Calculate and clean all pagination variables
        $this->total_items        = (int) max(0, $this->config['total_items']);
        $this->items_per_page     = (int) max(1, $this->config['items_per_page']);
        $this->total_pages        = (int) ceil($this->total_items / $this->items_per_page);
        $this->current_page       = (int) min(max(1, $this->current_page), max(1, $this->total_pages));
        $this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
        $this->current_last_item  = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);
        $this->previous_page      = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
        $this->next_page          = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;
        $this->first_page         = ($this->current_page === 1) ? FALSE : 1;
        $this->last_page          = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
        $this->offset             = (int) (($this->current_page - 1) * $this->items_per_page);
    }
}