<?

class EdFaq
{
    public static $faqTable;
    public static $name = 'Edit FAQs';
    public static $path;
    public static $topicTable;
    public static $shortCode;
    public static $tablePrefix = 'edfaq';
    public static $wpdb;
    public static $uid;
    public static $url;
    
    public static function addQues()
    {
		$ordr = self::$wpdb->get_var(sPrintF('
            SELECT MAX(ordr)
            FROM %s
            WHERE topicId = %s',
            self::$faqTable, $_REQUEST['topicId']
        ))+1;
		
        self::$wpdb->insert(self::$faqTable,
            Array(
                'ques' => mysql_real_escape_string(trim($_REQUEST['ques'])),
				'answr' => mysql_real_escape_string(trim($_REQUEST['answr'])),
                'published' => $_REQUEST['published'],
				'topicId' => $_REQUEST['topicId'],
				'ordr' => $ordr
            ),
            Array('%s', '%s', '%d', '%d', '%d')
        );
    }
    
    public static function addTopic()
    {
        self::$wpdb->insert(self::$topicTable, Array('name' => trim($_REQUEST['name'])));
    }
    
    public static function deleteQues()
    {
        $currentPos = self::$wpdb->get_var(sPrintF('
            SELECT ordr
            FROM %s
            WHERE id = %s',
            self::$faqTable, $_REQUEST['id']
        ));
        
        self::$wpdb->query(sPrintF('
            DELETE FROM %s
            WHERE id = %s',
            self::$faqTable, $_REQUEST['id']
        ));
        
        self::$wpdb->query(sPrintF('
            UPDATE %s
            SET ordr = ordr-1
            WHERE topicId = %s AND
                  ordr > %s',
            self::$faqTable, $_REQUEST['topicId'], $currentPos
        ));
        die();
    }
    
    public static function deleteTopic()
    {
        self::$wpdb->query(sPrintF('
            DELETE FROM %s
            WHERE topicId = %s',
            self::$faqTable, $_REQUEST['id']
        ));
        
        self::$wpdb->query(sPrintF('
            DELETE FROM %s
            WHERE id = %s',
            self::$topicTable, $_REQUEST['id']
        ));
        die();
    }
    
    public static function editQues()
    {
        self::$wpdb->update(self::$faqTable,
            Array('answr' => trim($_REQUEST['answr'])), Array('id' => $_REQUEST['id']),
            Array('%s'), Array('%d')
        );
    }
    
    public static function getAnswer()
    {
        echo self::$wpdb->get_Var(sPrintF('
            SELECT answr
            FROM %s
            WHERE id = %s',
            self::$faqTable, $_REQUEST['id']
        ));
		die();
    }
    
    public static function getFaqs()
    {
        return self::$wpdb->get_Results(sPrintF('
            SELECT id, ques, answr, published
            FROM %s
            WHERE topicId = %s
            ORDER BY ordr',
            self::$faqTable, $_REQUEST['topicId']
        ));
    }
    
    public static function getTopic()
    {
        return self::$wpdb->get_var(sPrintF('
            SELECT name
            FROM %s
            WHERE id = %s',
            self::$topicTable, $_REQUEST['topicId']
        ));
    }
    
    public static function getTopics()
    {
        return self::$wpdb->get_Results(sPrintF('
            SELECT id, name, (SELECT COUNT(*) FROM %s WHERE topicId = %s.id) count
            FROM %s
            ORDER BY id DESC',
			self::$faqTable, self::$topicTable,
			self::$topicTable
        ));
    }
    
    public static function init()
    {
        // Ajax actions.
        add_Action('wp_ajax_edFaqDeleteTopic', 'EdFaq::deleteTopic');
        add_Action('wp_ajax_edFaqGetAnswer', 'EdFaq::getAnswer');
        add_Action('wp_ajax_edFaqPublishFaq', 'EdFaq::publishFaq');
        add_Action('wp_ajax_edFaqDeleteQues', 'EdFaq::deleteQues');
        add_Action('wp_ajax_edFaqSortQues', 'EdFaq::sortQues');
        
        self::$faqTable = sPrintF('%s%s_%s', self::$wpdb->prefix, self::$tablePrefix, 'faq');
        self::$topicTable = sPrintF('%s%s_%s', self::$wpdb->prefix, self::$tablePrefix, 'topic');
        self::$shortCode = str_Replace(' ', '', uCWords(strToLower(self::$name)));
        self::$uid = str_Replace(' ', '-', strToLower(self::$name));
		self::$url = plugins_Url(self::$uid.'/');
		
        add_Action('admin_menu', function(){
            add_Plugins_Page(EdFaq::$name, EdFaq::$name, 'manage_options', EdFaq::$uid, 'EdFaq::mainPage');
        });
		
        add_ShortCode(self::$shortCode, 'EdFaq::shortCodeCallback');
    }
    
    public static function install()
    {
		global $wpdb;
		self::$wpdb = $wpdb;
        if (self::$wpdb->get_Var(sPrintF('SHOW TABLES LIKE "%s"', self::$faqTable)) != self::$faqTable) // First run.
        {
            self::$wpdb->query(sPrintF('
                CREATE TABLE %s(
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL UNIQUE
                )', self::$topicTable
            ));
            
            self::$wpdb->query(sPrintF('
                CREATE TABLE %s(
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    topicId INT NOT NULL,
                    ques VARCHAR(255) NOT NULL,
                    answr TEXT DEFAULT NULL,
                    ordr INT NOT NULL DEFAULT 0,
                    published BOOLEAN NOT NULL DEFAULT FALSE,
                    FOREIGN KEY(topicId) REFERENCES %s(id)
                )', self::$faqTable, self::$topicTable
            ));
        }
    }
    
    public static function publishFaq()
    {
        print_R($_REQUEST);
        self::$wpdb->update(self::$faqTable,
            Array('published' => $_REQUEST['status']), Array('id' => $_REQUEST['id']),
            Array('%d'), Array('%d')
        );
        die();
    }
    
    public static function shortCodeCallback($atts)
    {
        if (isSet($atts['id']))
        {
            $faqs = self::$wpdb->get_Results(sPrintF('
                SELECT id,ques, answr
                FROM %s
                WHERE topicId = %s AND
                      published = TRUE
                ORDER BY ordr',
                self::$faqTable, $atts['id']
            ));
			
			ob_Start();
            include(self::$path.'faqPage.php');
			$output = ob_Get_Contents();;
			ob_End_Clean();
			return $output;
        }
    }
    
    public static function sortQues()
    {
        $currentPos = self::$wpdb->get_var(sPrintF('
            SELECT ordr
            FROM %s
            WHERE id = %s',
            self::$faqTable, $_REQUEST['id']
        ));
        $newPos = $_REQUEST['pos'];
        
        if ($newPos > $currentPos) // Moved downward eg: 2 to 4.
        {
            self::$wpdb->query(sPrintF('
                UPDATE %s
                SET ordr = ordr-1
                WHERE topicId = %s AND
                      ordr > %s AND ordr <= %s',
                self::$faqTable, $_REQUEST['topicId'],
                $currentPos, $newPos
            ));
        }
        else // Moved upward eg: 4 to 2.
        {
            self::$wpdb->query(sPrintF('
                UPDATE %s
                SET ordr = ordr+1
                WHERE topicId = %s AND
                      ordr >= %s AND ordr < %s',
                self::$faqTable, $_REQUEST['topicId'],
                $newPos, $currentPos
            ));
        }
        
        self::$wpdb->query(sPrintF('
            UPDATE %s
            SET ordr = %s
            WHERE id = %s',
            self::$faqTable, $newPos, $_REQUEST['id']
        ));
    }
    
    public static function mainPage()
    {
		$pageType = null;
        if (isSet($_REQUEST['topicId']) and ($topicName = self::getTopic()))
        {
            if (isSet($_REQUEST['action'])) eval(sPrintF('self::%s();', $_REQUEST['action']));
            $faqs = self::getFaqs();
			$pageType = 'faq';
        }
        else
		{
			if (isSet($_REQUEST['action'])) eval(sPrintF('self::%s();', $_REQUEST['action']));
			$topics = self::getTopics();
			$topicNames = Array();
			forEach ($topics as $topic) $topicNames[strToLower($topic->name)] = '';
			$pageType = 'topic';
		}
		require_Once(self::$path.'main.php');
    }
    
    public static function uninstall()
    {
        self::$wpdb->query(sPrintF('DROP TABLE %s', self::$faqTable));
        self::$wpdb->query(sPrintF('DROP TABLE %s', self::$topicTable));
    }
}

?>