<?php
class Pager
{
    protected $num_pages;
    protected $pg;
    protected $start;

    /**
     * @param $total
     * @param $limit
     */
    public function __construct($total, $limit)
    {
        $this->num_pages = $this->am_pages($total, $limit);
        $this->pg = $this->page($this->num_pages);
        $this->start = $limit * $this->pg - $limit;
    }

    /**
     * @param int $am_pages
     * @return int
     */
    public function page($am_pages = 1)
    {
        $page = 1;
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'end') $page = intval($am_pages);
            else if (is_numeric($_GET['page'])) $page = intval($_GET['page']);
        }
        if ($page < 1) $page = 1;
        if ($page > $am_pages) $page = $am_pages;
        return $page;
    }

    /**
     * @param int $am_posts
     * @param int $am_p_pages
     * @return float|int
     */
    public function am_pages($am_posts = 0, $am_p_pages = 10)
    {
        if ($am_posts != 0)
        {
            $v_pages = ceil($am_posts / $am_p_pages);
            return $v_pages;
        } else return 1;
    }

    /**
     * @param string $link
     * @param int $am_pages
     * @param int $page
     */
    public function pages($link = '?', $am_pages = 1, $page = 1)
    {
        if ($page < 1) $page = 1;
        echo '<div class="paginate">';
        if ($page != 1) echo '<a class="paginate-first" href="' . $link . 'page=1">&laquo;</a> ';
        if ($page != 1) echo '<a href="' . $link . 'page=1">1</a>';
        else echo '<a class="paginate-current">1</a>';

        for ($from = -3; $from <= 3; $from++)
        {
            if ($page + $from > 1 && $page + $from < $am_pages)
            {
                if ($from == -3 && $page + $from > 2) echo ' .. ';
                if ($from != 0) echo ' <a href="' . $link . 'page=' . ($page + $from) . '">' . ($page + $from) . '</a>';
                else echo ' <a class="paginate-current">' . ($page + $from) . '</a>';
                if ($from == 3 && $page + $from < $am_pages - 1) echo ' .. ';
            }
        }
        if ($page != $am_pages) echo ' <a href="' . $link . 'page=end">' . $am_pages . '</a>';
        else if ($am_pages > 1) echo ' <a class="paginate-current">' . $am_pages . '</a>';
        if ($page != $am_pages) echo ' <a class="paginate-last" href="' . $link . 'page=end">&raquo;</a>';
        echo '</div>
		<div class="clear"></div>';
    }

    /**
     * @param string $separator
     */
    public function view($separator = '?')
    {
        $link = preg_replace('/\?page=end|\?page=(.*?[0-9])|&page=(.*?)/i', '', $_SERVER['REQUEST_URI']) . $separator;

        if ($this->num_pages > 1) $this->pages($link, $this->num_pages, $this->pg);
    }

    /**
     * @return mixed
     */
    public function start()
    {
        return $this->start;
    }
}