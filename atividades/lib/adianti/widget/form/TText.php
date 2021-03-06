<?php
namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Adianti\Core\AdiantiCoreTranslator;
use Exception;

/**
 * Text Widget (also known as Memo)
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TText extends TField implements AdiantiWidgetInterface
{
    private   $height;
    private   $exitAction;
    protected $formName;
    protected $size;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        // creates a <textarea> tag
        $this->tag = new TElement('textarea');
        $this->tag->{'class'} = 'tfield';       // CSS
        
        // defines the text default height
        $this->height= 100;
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
        }
    }
    
    /**
     * Returns the size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array( $this->size, $this->height );
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        $this->tag-> name  = $this->name;   // tag name
        if ($this->size)
        {
            $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
        }
        
        if ($this->height)
        {
            $this->setProperty('style', "height:{$this->height}px", FALSE); //aggregate style info
        }
        
        // check if the field is not editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        if (isset($this->exitAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->exitAction->serialize(FALSE);
            $this->setProperty('onBlur', "serialform=(\$('#{$this->formName}').serialize());
                                          __adianti_ajax_lookup('$string_action&'+serialform, this)");
        }
        
        // add the content to the textarea
        $this->tag->add(htmlspecialchars($this->value));
        // show the tag
        $this->tag->show();
    }
}
