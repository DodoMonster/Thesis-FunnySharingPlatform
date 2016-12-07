<?php
namespace Addons\Sphinx;
class SearchUtil {
    /**
     * @var SphinxClient 
    **/
    protected $client;
    /**
     * @var string
    **/
    protected $keywords;
    /**
     * @var resource
    **/
    private static $dbconnection = null;
    private $db=null;
   
    /**
     * Constructor
     **/
    public function __construct($options = array()) {
        $defaults = array(
            'query_mode' => SPH_MATCH_EXTENDED2,
            'sort_mode' => SPH_SORT_EXTENDED,
            'ranking_mode' => SPH_RANK_PROXIMITY_BM25,
            'field_weights' => array(),
            'max_matches' => 1000,
            'snippet_enabled' => true,
            'snippet_index' => 'items',
            'snippet_fields' => array(), 
        );
        $this->options = array_merge($defaults, $options);
        $this->client = new \SphinxClient();
        $this->client->setMatchMode($this->options['query_mode']);
        if ($this->options['field_weights'] !== array()) {
            $this->client->setFieldWeights($this->options['field_weights']);
        }
        /*
        if ( in_array($this->options['query_mode'], [SPH_MATCH_EXTENDED2,SPH_MATCH_EXTENDED]) ) {
            $this->client->setRankingMode($this->options['ranking_mode']);
        }
        */
        $config=\Yaf_Registry::get("dbconfig")->db->toArray();
        $config['NAME']='sphinx_items';
        $this->db= new \Core\Dao\Db\DbInit($config,'','');
    }
   
    /**
     * Query
     *
     * @param string  $keywords
     * @param integer $offset
     * @param integer $limit
     * @param string  $index
     * @return array
     **/
    public function query($keywords, $offset = 0, $limit = 10, $index = '*') {
        $this->keywords = $keywords;
        $max_matches = $limit > $this->options['max_matches'] ? $limit : $this->options['max_matches'];
        $this->client->setLimits($offset, $limit, $max_matches);
        $query_results = $this->client->query($keywords, $index);

        if ($query_results === false) {
            $this->log('error:' . $this->client->getLastError());
        }
               
        $res = [];
        if ( empty($query_results['matches']) ) {
            return $res;
        }
        $res['total'] = $query_results['total'];
        $res['total_found'] = $query_results['total_found'];
        $res['time'] = $query_results['time'];
        $doc_ids = array_keys($query_results['matches']);
        unset($query_results);
        $res['data'] = $this->fetch_data($doc_ids);
        if ($this->options['snippet_enabled']) {
            $this->buildExcerptRows($res['data']);
        }
       
        return $res;
    }
   
    /**
     * custom sorting 
     * 
     * @param string $sortBy
     * @param int $mode
     * @return bool
     **/
    public function setSortBy($sortBy = '', $mode = 0) {
        if ($sortBy) {
            $mode = $mode ?: $this->options['sort_mode'];
            $this->client->setSortMode($mode, $sortBy);
        } else {
            $this->client->setSortMode(SPH_SORT_RELEVANCE);
        }
    }
   
    /**
     * fetch data based on matched doc_ids
     * 
     * @param array $doc_ids
     * @return array
     **/   
    protected function fetch_data($doc_ids) {
        //$ids = implode(',', $doc_ids);
        //$queries = self::getDBConnection()->query("SELECT * FROM items WHERE id in ($ids)", PDO::FETCH_ASSOC);
        //print_r($doc_ids);
        $options['table']='items';
        $options['where']=array('id'=>array('IN',$doc_ids));
        $options['param']=$doc_ids;
        $queries=$this->db->select($options);
        //return iterator_to_array($queries);
        return $queries;
    }
   
    /**
     * build excerpts for data
     * 
     * @param array $rows
     * @return array
     **/ 
    protected function buildExcerptRows(&$rows) {
        $options = array(
            'before_match' => '<b style="color:red">',
            'after_match'  => '</b>',
            'chunk_separator' => '...',
            'limit'    => 256,
            'around'   => 3,
            'exact_phrase' => false,
            'single_passage' => true,
            'limit_words' => 5,
        );
        $scount = count($this->options['snippet_fields']);
        foreach ($rows as &$row) {
            foreach ($row as $fk => $item) {
                if (!is_string($item) || ($scount && !in_array($fk, $this->options['snippet_fields'])) ) continue;
                $item = preg_replace('/[\r\t\n]+/', '', strip_tags($item));
                $res = $this->client->buildExcerpts(array($item), $this->options['snippet_index'], $this->keywords, $options);
                $row[$fk] = $res === false ? $item : $res[0];
            }
        }       
        return $rows;
    }
   
    /**
     * database connection
     *
     * @return resource
     **/ 
    private static function getDBConnection() {       
        $dsn = 'mysql:host=192.168.1.191;dbname=sphinx_items';
        $user = 'root';
        $pass = '123456';
        if (!self::$dbconnection) {
            try {
                self::$dbconnection = new PDO($dsn, $user, $pass);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
        return self::$dbconnection;
        
    }

    /**
     * Chinese words segmentation
     *
     **/
    public function wordSplit($keywords) {
        $fpath = ini_get('scws.default.fpath');
        $so = scws_new();
        $so->set_charset('utf-8');
        $so->add_dict($fpath . '/dict.utf8.xdb');
        //$so->add_dict($fpath .'/custom_dict.txt', SCWS_XDICT_TXT);
        $so->set_rule($fpath . '/rules.utf8.ini');
        $so->set_ignore(true);
        $so->set_multi(false);
        $so->set_duality(false);
        $so->send_text($keywords);
        $words = [];
        $results =  $so->get_result();
        foreach ($results as $res) {
            $words[] = '(' . $res['word'] . ')';
        }
        $words[] = '(' . $keywords . ')';
        return join('|', $words);
    }
   
    /**
     * get current sphinx client
     *
     * @return resource
     **/
    public function getClient() {
        return $this->client;
    }
    /**
     * log error
     *
     **/
    public function log($msg) {
        // log errors here
        //echo $msg;
       
    }   
    /**
     * magic methods
     **/
    public function __call($method, $args) {
        $rc = new \ReflectionClass('SphinxClient');
        if ( !$rc->hasMethod($method) ) {
            throw new Exception('invalid method :' . $method);
        }
        return call_user_func_array(array($this->client, $method), $args);
    }
}

