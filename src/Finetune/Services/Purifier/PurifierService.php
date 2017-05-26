<?php
namespace Finetune\Finetune\Services\Purifier;

use Exception;

use HTMLPurifier;

use HTMLPurifier_Config;

use Illuminate\Contracts\Config\Repository;


class PurifierService {
    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var HTMLPurifier
     */
    protected $purifier;
    /**
     * Constructor
     *
     * @param Repository $config
     * @throws Exception
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->setUp();
    }
    /**
     * Setup
     *
     * @throws Exception
     */
    private function setUp()
    {
        if (!$this->config->has('purifier')) {
            throw new Exception('Configuration parameters not loaded!');
        }
        $config = $this->getConfig();

        // Create HTMLPurifier object
        $this->purifier = new HTMLPurifier($config);
    }
    /**
    /**
     * @param null $config
     *
     * @return mixed|null
     */
    protected function getConfig($config = null)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.SafeIframe', true);

        // Set some HTML5 properties
        $config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
        $config->set('HTML.DefinitionRev', 1);
        $finetuneConfig = config('purifier.settings.default');

        foreach($finetuneConfig as $key => $value){
            $config->set($key, $value);
        }

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('nav',     'Block', 'Flow', 'Common');
            $def->addElement('article', 'Block', 'Flow', 'Common');
            $def->addElement('aside',   'Block', 'Flow', 'Common');
            $def->addElement('header',  'Block', 'Flow', 'Common');
            $def->addElement('footer',  'Block', 'Flow', 'Common');
            $def->addElement('address', 'Block', 'Flow', 'Common');
            $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
            $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
            $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
            $def->addElement('s',    'Inline', 'Inline', 'Common');
            $def->addElement('var',  'Inline', 'Inline', 'Common');
            $def->addElement('sub',  'Inline', 'Inline', 'Common');
            $def->addElement('sup',  'Inline', 'Inline', 'Common');
            $def->addElement('mark', 'Inline', 'Inline', 'Common');
            $def->addElement('wbr',  'Inline', 'Empty', 'Core');
            $def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
            $def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
        }
        return $config;
    }
    /**
     * @param      $dirty
     * @param null $config
     *
     * @return mixed
     */
    public function clean($dirty, $config = null)
    {
        if (is_array($dirty)) {
            return array_map(function ($item) use ($config) {
                return $this->clean($item, $config);
            }, $dirty);
        }
        return $this->purifier->purify($dirty);
    }
    /**
     * Get HTMLPurifier instance.
     *
     * @return \HTMLPurifier
     */
    public function getInstance()
    {
        return $this->purifier;
    }
}
