/*
Copyright (c) 2007, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.2.2
*/
/**
* @module button
* @description <p>The Button Control enables the creation of rich, graphical 
* buttons that function like traditional HTML form buttons.  <em>Unlike</em> 
* tradition HTML form buttons, buttons created with the Button Control can have 
* a label that is different from its value.  With the inclusion of the optional 
* <a href="module_menu.html">Menu Control</a>, the Button Control can also be
* used to create menu buttons and split buttons, controls that are not 
* available natively in HTML.  The Button Control can also be thought of as a 
* way to create more visually engaging implementations of the browser's 
* default radio-button and check-box controls.</p>
* <p>The Button Control supports the following types:</p>
* <dl>
* <dt>button</dt>
* <dd>Basic push button that can execute a user-specified command when 
* pressed.</dd>
* <dt>link</dt>
* <dd>Navigates to a specified url when pressed.</dd>
* <dt>submit</dt>
* <dd>Submits the parent form when pressed.</dd>
* <dt>reset</dt>
* <dd>Resets the parent form when pressed.</dd>
* <dt>checkbox</dt>
* <dd>Maintains a "checked" state that can be toggled on and off.</dd>
* <dt>radio</dt>
* <dd>Maintains a "checked" state that can be toggled on and off.  Use with 
* the ButtonGroup class to create a set of controls that are mutually 
* exclusive; checking one button in the set will uncheck all others in 
* the group.</dd>
* <dt>menubutton</dt>
* <dd>When pressed will show/hide a menu.</dd>
* <dt>splitbutton</dt>
* <dd>Can execute a user-specified command or display a menu when pressed.</dd>
* </dl>
* @title Button
* @namespace YAHOO.widget
* @requires yahoo, dom, element, event
* @optional container, menu
* @beta
*/


(function() {

// Shorthard for utilities

var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    Lang = YAHOO.lang,


    // Private member variables

    m_oUserAgent = navigator.userAgent.toLowerCase(),
    m_bOpera = (m_oUserAgent.indexOf('opera') > -1),
    m_bSafari = (m_oUserAgent.indexOf('safari') > -1),
    m_bGecko = (!m_bOpera && !m_bSafari && m_oUserAgent.indexOf('gecko') > -1),
    m_bIE7 = (!m_bOpera && m_oUserAgent.indexOf('msie 7') > -1),
    m_bIE = (!m_bOpera && m_oUserAgent.indexOf('msie') > -1),
    m_oButtons = {},
    m_oFocusedButton = null;



//  Constructor


/**
* The Button class creates a rich, graphical button.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;input&#62;</code>, <code>&#60;a&#62;</code> or 
* <code>&#60;span&#62;</code> element to be used to create the button.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>|<a href="
* http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#
* ID-33759296">HTMLElement</a>} p_oElement Object reference for the 
* <code>&#60;input&#62;</code>, <code>&#60;a&#62;</code> or 
* <code>&#60;span&#62;</code> element to be used to create the button.
* @param {Object} p_oElement Object literal specifying a set of configuration  
* attributes used to create the button.
* @param {Object} p_oAttributes Optional. Object literal specifying a set of 
* configuration attributes used to create the button.
* @namespace YAHOO.widget
* @class Button
* @constructor
* @extends YAHOO.util.Element
*/
YAHOO.widget.Button = function(p_oElement, p_oAttributes) {

    var fnSuperClass = YAHOO.widget.Button.superclass.constructor;

    if(
        arguments.length == 1 && 
        !Lang.isString(p_oElement) && 
        !p_oElement.nodeName
    ) {

        if(!p_oElement.id) {

            p_oElement.id = Dom.generateId();

            YAHOO.log(
                "No value specified for the button's \"id\" attribute. " +
                "Setting  button id to \"" + p_oElement.id + "\".",
                "warn"
            );

        }

        this.logger = new YAHOO.widget.LogWriter("Button " + p_oElement.id);

        this.logger.log(
                "No source HTML element.  "  +
                "Building the button using the set of configuration attributes."
            );

        fnSuperClass.call(
            this,
            (this._createButtonElement(p_oElement.type)),
            p_oElement
        );

    }
    else {

        var oConfig = {
        
            element: null,
            attributes: (p_oAttributes || {})
            
        },

        sTagName;


        if(Lang.isString(p_oElement)) {

            var oElement = Dom.get(p_oElement);

            if (oElement) {

                sTagName = oElement.tagName.toUpperCase();
            
                if(sTagName == this.TAG_NAME) {
            
                    oConfig.attributes.id = oElement.id;
            
                }
                else if(sTagName == "INPUT" && !oConfig.attributes.id) {
            
                    oConfig.attributes.id = Dom.generateId();
            
                    YAHOO.log(
                        "No value specified for the button's \"id\" " +
                        "attribute. Setting button id to \"" + 
                        oConfig.attributes.id + "\".",
                        "warn"
                    );
                
                }
            
                this.logger = new YAHOO.widget.LogWriter(
                                    "Button " + oConfig.attributes.id
                                        );
            
                this.logger.log(
                        "Building the button using an existing HTML " + 
                        "element as a source element." 
                    );
            
            
                oConfig.attributes.srcelement = oElement;
            
                initConfig.call(this, oConfig);
            
            
                if(!oConfig.element) {
            
                    this.logger.log(
                            "Source element could not be used as is.  " +
                            "Creating a new HTML element for the button."
                        );
            
                    oConfig.element = 
                        this._createButtonElement(oConfig.attributes.type);
            
                }
            
                fnSuperClass.call(this, oConfig.element, oConfig.attributes);

            }

        }
        else {

            sTagName = p_oElement.tagName.toUpperCase();

            if(sTagName == this.TAG_NAME) {

                if(p_oElement.id) {

                    oConfig.attributes.id = p_oElement.id;
                
                }
                else {

                    oConfig.attributes.id = Dom.generateId();

                    YAHOO.log(
                        "No value specified for the button's \"id\" " +
                        "attribute. Setting button id to \"" + 
                        oConfig.attributes.id + "\".",
                        "warn"
                    );

                }


            }
            else if(sTagName == "INPUT" && !oConfig.attributes.id) {

                oConfig.attributes.id = Dom.generateId();

                YAHOO.log(
                    "No value specified for the button's \"id\" " +
                    "attribute. Setting button id to \"" + 
                    oConfig.attributes.id + "\".",
                    "warn"
                );
            
            }

            this.logger = 
                new YAHOO.widget.LogWriter("Button " + oConfig.attributes.id);

            this.logger.log(
                    "Building the button using an existing HTML element as " + 
                    "a source element." 
                );


            oConfig.attributes.srcelement = p_oElement;
    
            initConfig.call(this, oConfig);
    
    
            if(!oConfig.element) {

                this.logger.log(
                        "Source element could not be used as is.  " +
                        "Creating a new HTML element for the button."
                    );
        
                oConfig.element = 
                    this._createButtonElement(oConfig.attributes.type);
        
            }
        
            fnSuperClass.call(this, oConfig.element, oConfig.attributes);
        
        }

    }

};



// Private methods


/**
* @method getFirstElement
* @description Returns an HTML element's first HTML element node.
* @private
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-58190037">HTMLElement</a>} p_oElement Object 
* reference specifying the element to be evaluated.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-58190037">HTMLElement</a>}
*/
function getFirstElement(p_oElement) {

    var oFirstChild = p_oElement.firstChild;

    if(oFirstChild) {

        if(oFirstChild.nodeType == 1) {

            return oFirstChild;

        }
        else {

            var oNextSibling = oFirstChild.nextSibling;

            if(oNextSibling && oNextSibling.nodeType == 1) {
            
                return oNextSibling;
            
            }

        }

    }

}


/**
* @method createInputElement
* @description Creates an <code>&#60;input&#62;</code> element of the 
* specified type.
* @private
* @param {String} p_sType String specifying the type of 
* <code>&#60;input&#62;</code> element to create.
* @param {String} p_sName String specifying the name of 
* <code>&#60;input&#62;</code> element to create.
* @param {String} p_sValue String specifying the value of 
* <code>&#60;input&#62;</code> element to create.
* @param {String} p_bChecked Boolean specifying if the  
* <code>&#60;input&#62;</code> element is to be checked.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>}
*/
function createInputElement(p_sType, p_sName, p_sValue, p_bChecked) {

    var oInput;

    if(m_bIE) {

        /*
            For IE it is necessary to create the element with the 
            "type," "name," "value," and "checked" properties set all at once.
        */
    
        var sInput = "<input type=\"" + p_sType + "\" name=\"" + p_sName + "\"";

        if(p_bChecked) {

            sInput += " checked";
        
        }
        
        sInput += ">";

        oInput = document.createElement(sInput);

    }
    else {
    
        oInput = document.createElement("input");
        oInput.name = p_sName;
        oInput.type = p_sType;

        if(p_bChecked) {

            oInput.checked = true;
        
        }

    }

    oInput.value = p_sValue;
    
    return oInput;

}


/**
* @method setAttributesFromSrcElement
* @description Gets the values for all the attributes of the source element 
* (either <code>&#60;input&#62;</code> or <code>&#60;a&#62;</code>) that map to
* Button configuration attributes and sets them into a collection that is
* passed to the Button constructor.
* @private
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>|<a href="http://www.w3.org/
* TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#ID-
* 48250443">HTMLAnchorElement</a>} p_oElement Object reference to the HTML 
* element (either <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code>) 
* used to create the button.
* @param {Object} p_oAttributes Object reference for the collection of 
* configuration attributes used to create the button.
*/
function setAttributesFromSrcElement(p_oElement, p_oAttributes) {

    var me = this;

    /**
    * @method setAttributeFromDOMAttribute
    * @description Gets the value of the specified DOM attribute and sets it 
    * into the collection of configuration attributes used to configure 
    * the button.
    * @private
    * @param {String} p_sAttribute String representing the name of the 
    * attribute to retrieve from the DOM element.
    */
    function setAttributeFromDOMAttribute(p_sAttribute) {

        if( !(p_sAttribute in p_oAttributes) ) {

            /*
                Need to use "getAttributeNode" instead of "getAttribute" 
                because using "getAttribute," IE will return the innerText of 
                a <code>&#60;button&#62;</code> for the value attribute rather 
                than the value of the "value" attribute.
            */
    
            var oAttribute = p_oElement.getAttributeNode(p_sAttribute);
    

            if(oAttribute && ("value" in oAttribute)) {

                me.logger.log(
                    "Setting attribute \"" + p_sAttribute + "\" using " +
                    "source element's attribute value of \"" + 
                    oAttribute.value + "\""
                );

                p_oAttributes[p_sAttribute] = oAttribute.value;

            }

        }
    
    }


    /**
    * @method setFormElementProperties
    * @description Gets the value of the attributes from the form element and 
    * sets them into the collection of configuration attributes used to 
    * configure the button.
    * @private
    */
    function setFormElementProperties() {

        setAttributeFromDOMAttribute("type");

        if( !("disabled" in p_oAttributes) ) {

            p_oAttributes.disabled = p_oElement.disabled;

        }

        setAttributeFromDOMAttribute("name");
        setAttributeFromDOMAttribute("value");
        setAttributeFromDOMAttribute("title");

    }


    var sSrcElementTagName = p_oElement.tagName.toUpperCase();


    if( !("label" in p_oAttributes) ) {

        // Set the "label" property
    
        var sText = sSrcElementTagName == "INPUT" ? 
                        p_oElement.value : p_oElement.innerHTML;
    
    
        if(sText && sText.length > 0) {
            
            p_oAttributes.label = sText;
            
        } 

    }


    setAttributeFromDOMAttribute("tabindex");
    setAttributeFromDOMAttribute("accesskey");


    switch(sSrcElementTagName) {
    
        case "A":
        
            p_oAttributes.type = "link";

            setAttributeFromDOMAttribute("href");
            setAttributeFromDOMAttribute("target");

        break;

        case "INPUT":

            setFormElementProperties();

            if( !("checked" in p_oAttributes) ) {
    
                p_oAttributes.checked = p_oElement.checked;
    
            }

        break;

        case "BUTTON":

            setFormElementProperties();

            var oRootNode = p_oElement.parentNode.parentNode;

            if(Dom.hasClass(oRootNode, "checked")) {
            
                p_oAttributes.checked = true;
            
            }

            if(Dom.hasClass(oRootNode, "disabled")) {

                p_oAttributes.disabled = true;
            
            }

            p_oElement.removeAttribute("name");
            p_oElement.removeAttribute("value");

            p_oElement.setAttribute("type", "button");

        break;
    
    }

}


/**
* @method initConfig
* @description Initializes the set of configuration attributes that are used to  
* instantiate the button.
* @private
* @param {Object} Object representing the button's set of 
* configuration attributes.
*/
function initConfig(p_oConfig) {

    var oAttributes = p_oConfig.attributes,
        oSrcElement = oAttributes.srcelement,
        sSrcElementTagName = oSrcElement.tagName.toUpperCase();


    if(sSrcElementTagName == this.TAG_NAME) {

        p_oConfig.element = oSrcElement;

        var oFirstChild = getFirstElement(p_oConfig.element);

        Dom.addClass(oFirstChild, "first-child");

        if(oFirstChild) {

            var oButton = getFirstElement(oFirstChild);


            if(oButton) {

                var sButtonTagName = oButton.tagName.toUpperCase();


                if(sButtonTagName == "A" || sButtonTagName == "BUTTON") {

                    setAttributesFromSrcElement.call(
                            this, 
                            oButton, 
                            oAttributes
                        );
                
                }
            
            }

        }
    
    }
    else if(sSrcElementTagName == "INPUT") {

        setAttributesFromSrcElement.call(this, oSrcElement, oAttributes);
    
    }

}


YAHOO.extend(YAHOO.widget.Button, YAHOO.util.Element, {


// Protected properties


/** 
* @property _button
* @description Object reference to the button's internal 
* <code>&#60;a&#62;</code> or <code>&#60;button&#62;</code> element.
* @default null
* @protected
* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-48250443">HTMLAnchorElement</a>|<a href="
* http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#ID-
* 34812697">HTMLButtonElement</a>
*/
_button: null,


/** 
* @property _menu
* @description Object reference to the button's menu.
* @default null
* @protected
* @type <a href="YAHOO.widget.Menu.html">YAHOO.widget.Menu</a>
*/
_menu: null,


/** 
* @property _hiddenField
* @description Object reference to the <code>&#60;input&#62;</code> element 
* used when the button's parent form is submitted.
* @default null
* @protected
* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>
*/
_hiddenField: null,


/** 
* @property _onclickAttributeValue
* @description Object reference to the button's current value for the "onclick"
* configuration attribute.
* @default null
* @protected
* @type Object
*/
_onclickAttributeValue: null,


/** 
* @property _activationKeyPressed
* @description Boolean indicating if the key(s) that toggle the button's 
* "active" state have been pressed.
* @default false
* @protected
* @type Boolean
*/
_activationKeyPressed: false,


/** 
* @property _activationButtonPressed
* @description Boolean indicating if the mouse button that toggles the button's
* "active" state has been pressed.
* @default false
* @protected
* @type Boolean
*/
_activationButtonPressed: false,


/** 
* @property _hasKeyEventHandlers
* @description Boolean indicating if the button's "blur", "keydown" and 
* "keyup" event handlers are assigned
* @default false
* @protected
* @type Boolean
*/
_hasKeyEventHandlers: false,


/** 
* @property _hasMouseEventHandlers
* @description Boolean indicating if the button's "mouseout" and "mousedown" 
* and "mouseup" event handlers are assigned
* @default false
* @protected
* @type Boolean
*/
_hasMouseEventHandlers: false,



// Constants


/**
* @property TAG_NAME
* @description The name of the tag to be used for the button's root element. 
* @default "SPAN"
* @final
* @type String
*/
TAG_NAME: "SPAN",


/**
* @property CHECK_ACTIVATION_KEYS
* @description Array of numbers representing keys that (when pressed) toggle 
* the button's "checked" attribute.
* @default [32]
* @final
* @type Array
*/
CHECK_ACTIVATION_KEYS: [32],


/**
* @property ACTIVATION_KEYS
* @description Array of numbers representing keys that (when presed) toggle 
* the button's "active" state.
* @default [13, 32]
* @final
* @type Array
*/
ACTIVATION_KEYS: [13, 32],


/**
* @property OPTION_AREA_WIDTH
* @description Width (in pixels) of the area of a split button that when 
* pressed will display a menu.
* @default 20
* @final
* @type Number
*/
OPTION_AREA_WIDTH: 20,


/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the 
* button's root element.
* @default "yuibutton"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuibutton",


/**
* @property RADIO_DEFAULT_TITLE
* @description String representing the default title applied to buttons of 
* type "radio." 
* @default "Unchecked.  Click to check."
* @final
* @type String
*/
RADIO_DEFAULT_TITLE: "Unchecked.  Click to check.",


/**
* @property RADIO_CHECKED_TITLE
* @description String representing the title applied to buttons of type "radio" 
* when checked.
* @default "Checked.  Click to uncheck."
* @final
* @type String
*/
RADIO_CHECKED_TITLE: "Checked.  Click to uncheck.",


/**
* @property CHECKBOX_DEFAULT_TITLE
* @description String representing the default title applied to buttons of 
* type "checkbox." 
* @default "Unchecked.  Click to check."
* @final
* @type String
*/
CHECKBOX_DEFAULT_TITLE: "Unchecked.  Click to check.",


/**
* @property CHECKBOX_CHECKED_TITLE
* @description String representing the title applied to buttons of type 
* "checkbox" when checked.
* @default "Checked.  Click to uncheck."
* @final
* @type String
*/
CHECKBOX_CHECKED_TITLE: "Checked.  Click to uncheck.",


/**
* @property MENUBUTTON_DEFAULT_TITLE
* @description String representing the default title applied to buttons of 
* type "menubutton." 
* @default "Menu collapsed.  Click to expand."
* @final
* @type String
*/
MENUBUTTON_DEFAULT_TITLE: "Menu collapsed.  Click to expand.",


/**
* @property MENUBUTTON_MENU_VISIBLE_TITLE
* @description String representing the title applied to buttons of type 
* "menubutton" when the button's menu is visible. 
* @default "Menu expanded.  Click or press Esc to collapse."
* @final
* @type String
*/
MENUBUTTON_MENU_VISIBLE_TITLE: 
    "Menu expanded.  Click or press Esc to collapse.",


/**
* @property SPLITBUTTON_DEFAULT_TITLE
* @description  String representing the default title applied to buttons of 
* type "splitebutton." 
* @default "Menu collapsed.  Click inside option region or press 
* Ctrl + Shift + M to show the menu."
* @final
* @type String
*/
SPLITBUTTON_DEFAULT_TITLE: 
    "Menu collapsed.  Click inside option region or press Ctrl + Shift + M to show the menu.",


/**
* @property SPLITBUTTON_OPTION_VISIBLE_TITLE
* @description String representing the title applied to buttons of type 
* "splitbutton" when the button's menu is visible. 
* @default "Menu expanded.  Press Esc or Ctrl + Shift + M to hide the menu."
* @final
* @type String
*/
SPLITBUTTON_OPTION_VISIBLE_TITLE: 
    "Menu expanded.  Press Esc or Ctrl + Shift + M to hide the menu.",


/**
* @property SUBMIT_TITLE
* @description String representing the title applied to buttons of 
* type "submit." 
* @default "Click to submit form."
* @final
* @type String
*/
SUBMIT_TITLE: "Click to submit form.",



// Protected attribute setter methods


/**
* @method _setType
* @description Sets the value of the button's "type" attribute.
* @protected
* @param {String} p_sType String indicating the value for the button's 
* "type" attribute.
*/
_setType: function(p_sType) {

    if(p_sType == "splitbutton") {

        this.on("option", this._onOption);

    }

},


/**
* @method _setLabel
* @description Sets the value of the button's "label" attribute.
* @protected
* @param {String} p_sLabel String indicating the value for the button's 
* "label" attribute.
*/
_setLabel: function(p_sLabel) {

    this._button.innerHTML = p_sLabel;                

},


/**
* @method _setTabIndex
* @description Sets the value of the button's "tabindex" attribute.
* @protected
* @param {Number} p_nTabIndex Number indicating the value for the button's 
* "tabindex" attribute.
*/
_setTabIndex: function(p_nTabIndex) {

    this._button.tabIndex = p_nTabIndex;

},


/**
* @method _setTitle
* @description Sets the value of the button's "title" attribute.
* @protected
* @param {String} p_nTabIndex Number indicating the value for the button's 
* "title" attribute.
*/
_setTitle: function(p_sTitle) {

    if(this.get("type") != "link") {

        var sTitle = p_sTitle;

        if(!sTitle) {


            var sType = this.get("type");

            switch(sType) {

                case "radio":

                    sTitle = this.RADIO_DEFAULT_TITLE;

                break;

                case "checkbox":

                    sTitle = this.CHECKBOX_DEFAULT_TITLE;

                break;
                
                case "menubutton":

                    sTitle = this.MENUBUTTON_DEFAULT_TITLE;

                break;

                case "splitbutton":

                    sTitle = this.SPLITBUTTON_DEFAULT_TITLE;

                break;

                case "submit":

                    sTitle = this.SUBMIT_TITLE;

                break;

            }

        }

        this._button.title = sTitle;

    }

},


/**
* @method _setDisabled
* @description Sets the value of the button's "disabled" attribute.
* @protected
* @param {Boolean} p_bDisabled Boolean indicating the value for the button's 
* "disabled" attribute.
*/
_setDisabled: function(p_bDisabled) {

    if(this.get("type") != "link") {

        if(p_bDisabled) {

            if(this.hasFocus()) {
            
                this.blur();
            
            }

            this._button.setAttribute("disabled", "disabled");

            this.addClass("disabled");

        }
        else {

            this._button.removeAttribute("disabled");

            this.removeClass("disabled");
        
        }

    }

},


/**
* @method _setAccessKey
* @description Sets the value of the button's "accesskey" attribute.
* @protected
* @param {String} p_sAccessKey String indicating the value for the button's 
* "accesskey" attribute.
*/
_setAccessKey: function(p_sAccessKey) {

    this._button.accessKey = p_sAccessKey;

},


/**
* @method _setHref
* @description Sets the value of the button's "href" attribute.
* @protected
* @param {String} p_sHref String indicating the value for the button's 
* "href" attribute.
*/
_setHref: function(p_sHref) {

    if(this.get("type") == "link") {

        this._button.href = p_sHref;
    
    }

},


/**
* @method _setTarget
* @description Sets the value of the button's "target" attribute.
* @protected
* @param {String} p_sTarget String indicating the value for the button's 
* "target" attribute.
*/
_setTarget: function(p_sTarget) {

    if(this.get("type") == "link") {

        this._button.setAttribute("target", p_sTarget);
    
    }

},


/**
* @method _setChecked
* @description Sets the value of the button's "target" attribute.
* @protected
* @param {Boolean} p_bChecked Boolean indicating the value for the button's 
* "checked" attribute.
*/
_setChecked: function(p_bChecked) {

    var sType = this.get("type"),
        sTitle;

    if(sType == "checkbox" || sType == "radio") {

        if(p_bChecked) {

            this.addClass("checked");
            
            sTitle = (sType == "radio") ? 
                        this.RADIO_CHECKED_TITLE : 
                        this.CHECKBOX_CHECKED_TITLE;
        
        }
        else {

            this.removeClass("checked");

            sTitle = (sType == "radio") ? 
                        this.RADIO_DEFAULT_TITLE : 
                        this.CHECKBOX_DEFAULT_TITLE;
        
        }

        this.set("title", sTitle);

    }

},


/**
* @method _setMenu
* @description Sets the value of the button's "menu" attribute.
* @protected
* @param {Object} p_oMenu Object indicating the value for the button's 
* "menu" attribute.
*/
_setMenu: function(p_oMenu) {

    var Menu = YAHOO.widget.Menu,
        oMenu,
        me = this;

    if(!Menu) {

        this.logger.log("YAHOO.widget.Menu dependency not met.", "error");

        return false;
    
    }


    function initMenu() {
    
        if(oMenu) {
    
            oMenu.showEvent.subscribe(this._onMenuShow, this, true);
            oMenu.hideEvent.subscribe(this._onMenuHide, this, true);
            oMenu.keyDownEvent.subscribe(this._onMenuKeyDown, this, true);
            oMenu.renderEvent.subscribe(this._onMenuRender, this, true);
            oMenu.clickEvent.subscribe(this._onMenuClick, this, true);
            oMenu.itemAddedEvent.subscribe(this._onMenuItemAdded, this, true);

            var oSrcElement = oMenu.srcElement;
    
            if(oSrcElement && oSrcElement.tagName.toUpperCase() == "SELECT") {
    
                oSrcElement.style.display = "none";
                oSrcElement.parentNode.removeChild(oSrcElement);
    
            }
    
            this._menu = oMenu;
    
        }
        else {
    
            this._menu.destroy();
            this._menu = null;
    
        }
    
    }


    if(p_oMenu instanceof Menu) {

        oMenu = p_oMenu;
        
        var aItems = oMenu.getItems(),
            nItems = aItems.length,
            oItem;
    
        if(nItems > 0) {
    
            var i = nItems - 1;
    
            do {
    
                oItem = aItems[i];
    
                if(oItem) {
    
                    oItem.cfg.subscribeToConfigEvent(
                                "selected", 
                                this._onMenuItemSelected, 
                                oItem, 
                                this
                            );
    
                }
            
            }
            while(i--);
    
        }

        initMenu.call(this);

    }
    else if(Lang.isArray(p_oMenu)) {

        this.on("appendTo", function() {

            oMenu = new Menu(
                            Dom.generateId(), 
                            { lazyload: true, itemdata: p_oMenu }
                        );
    
            initMenu.call(me);
        
        });
       
    }
    else if(Lang.isString(p_oMenu)) {

        Event.onContentReady(p_oMenu, function() {

            oMenu = new Menu(this, { lazyload: true });

            initMenu.call(me);
        
        });
    
    }
    else if(p_oMenu && p_oMenu.nodeName) {

        oMenu = new Menu(p_oMenu, { lazyload: true });
    
        initMenu.call(this);
    
    }

},


/**
* @method _setOnClick
* @description Sets the value of the button's "onclick" attribute.
* @protected
* @param {Object} p_oObject Object indicating the value for the button's 
* "onclick" attribute.
*/
_setOnClick: function(p_oObject) {

    /*
        Remove any existing listeners if a "click" event handler has already 
        been specified.
    */

    if(
        this._onclickAttributeValue && 
        (this._onclickAttributeValue != p_oObject)
    ) {

        this.removeListener("click", this._onclickAttributeValue.fn);

        this._onclickAttributeValue = null;

    }


    if(
        !this._onclickAttributeValue && 
        Lang.isObject(p_oObject) && 
        Lang.isFunction(p_oObject.fn)
    ) {

        this.on("click", p_oObject.fn, p_oObject.obj, p_oObject.scope);

        this._onclickAttributeValue = p_oObject;

    }

},



// Protected methods


/**
* @method _createButtonElement
* @description Creates the button's element.
* @protected
* @param {String} p_sType String indicating the type of element to create.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-58190037">HTMLElement</a>}
*/
_createButtonElement: function(p_sType) {

    var sTagName = this.TAG_NAME,
        oElement = document.createElement(sTagName);

    oElement.innerHTML =  
    
        "<" + sTagName + " class=\"first-child\">" + 
        (p_sType == "link" ? "<a></a>" : "<button type=\"button\"></button>") + 
        "</" + sTagName + ">";

    return oElement;

},


/**
* @method _isActivationKey
* @description Determines if the specified keycode is one that toggles the 
* button's "active" state.
* @protected
* @param {Number} p_nKeyCode Number representing the keycode to be evaluated.
* @return {Boolean}
*/
_isActivationKey: function(p_nKeyCode) {

    var sType = this.get("type"),
        aKeyCodes = (sType == "checkbox" || sType == "radio") ? 
            this.CHECK_ACTIVATION_KEYS : this.ACTIVATION_KEYS,

        nKeyCodes = aKeyCodes.length;

    if(nKeyCodes > 0) {

        var i = nKeyCodes - 1;

        do {

            if(p_nKeyCode == aKeyCodes[i]) {

                return true;

            }

        }
        while(i--);
    
    }

},


/**
* @method _isSplitButtonOptionKey
* @description Determines if the specified keycode is one that toggles the 
* display of the split button's menu.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
* @return {Boolean}
*/
_isSplitButtonOptionKey: function(p_oEvent) {

    return (
        p_oEvent.ctrlKey && 
        p_oEvent.shiftKey && 
        Event.getCharCode(p_oEvent) == 77
    );

},


/**
* @method _addListenersToForm
* @description Adds event handlers to the button's form.
* @protected
*/
_addListenersToForm: function() {

    var oForm = this.getForm();

    if(oForm) {

        Event.on(oForm, "reset", this._onFormReset, null, this);
        Event.on(oForm, "submit", this._onFormSubmit, null, this);

        var oSrcElement = this.get("srcelement");


        if (
            (m_bIE || m_bGecko) && 
            (
                this.get("type") == "submit" || 
                (oSrcElement && oSrcElement.type == "submit")
            )
        ) {
        
            var aListeners = Event.getListeners(oForm, "keydown"),
                bHasKeyDownListener = false;
    
            if(aListeners) {
    
                var nListeners = aListeners.length;
    
                if(nListeners > 0) {
    
                    var i = nListeners - 1;
                    
                    do {
       
                        if(
                            aListeners[i].fn == 
                            YAHOO.widget.Button.onFormKeyDown
                        ) {
        
                            bHasKeyDownListener = true;
                            break;
                        
                        }
        
                    }
                    while(i--);
                
                }
            
            }
    
    
            if(!bHasKeyDownListener) {
    
                Event.on(
                        oForm, 
                        "keydown", 
                        YAHOO.widget.Button.onFormKeyDown, 
                        null, 
                        this
                    );
    
            }

        }
    
    }

},


_originalMaxHeight: -1,


/**
* @method _showMenu
* @description Shows the button's menu.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event) that triggered the display of
* the menu.
*/
_showMenu: function(p_oEvent) {

    var oMenu = this._menu;

    if(oMenu) {

        YAHOO.widget.MenuManager.hideVisible();

        oMenu.cfg.applyConfig({
                context:[this.get("id"), "tl", "bl"], 
                clicktohide: false,
                constraintoviewport: false,
                visible: true
            });
            
        oMenu.cfg.fireQueue();

        /*
            Stop the propagation of the event so that the MenuManager 
            doesn't blur the menu after it gets focus.
        */

        if(p_oEvent.type == "mousedown") {

            Event.stopPropagation(p_oEvent);

        }

        this._menu.focus(); 


        var nViewportHeight = Dom.getViewportHeight(),
            nMenuHeight = oMenu.element.offsetHeight;


        if((oMenu.cfg.getProperty("y") + nMenuHeight) > nViewportHeight) {

            this.logger.log("Current menu position will place a portion, or " +
                "the entire menu outside the boundary of the viewport.  " + 
                "Repositioning the menu to stay inside the viewport.");

            oMenu.align("bl", "tl");

            var nY = oMenu.cfg.getProperty("y"),

                nScrollTop = (
                                document.documentElement.scrollTop || 
                                document.body.scrollTop
                            );
            

            if(nScrollTop >= nY) {

                if(this._originalMaxHeight == -1) {

                    this._originalMaxHeight = 
                            oMenu.cfg.getProperty("maxheight");

                }

                oMenu.cfg.setProperty(
                            "maxheight", 
                            (nMenuHeight - ((nScrollTop - nY) + 20))
                        );

                oMenu.align("bl", "tl");

            }

        }

        oMenu.cfg.setProperty("constraintoviewport", true);

    }            

},


/**
* @method _hideMenu
* @description Hides the button's menu.
* @protected
*/
_hideMenu: function() {

    var oMenu = this._menu;

    if(oMenu && oMenu.cfg.getProperty("visible")) {

        oMenu.hide();

    }

},




// Protected event handlers


/**
* @method _onMouseOver
* @description "mouseover" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onMouseOver: function(p_oEvent) {

    if(!this._hasMouseEventHandlers) {

        this.on("mouseout", this._onMouseOut);
        this.on("mousedown", this._onMouseDown);
        this.on("mouseup", this._onMouseUp);

        this._hasMouseEventHandlers = true;

    }

    this.addClass("hover");

    if(this._activationButtonPressed) {

        this.addClass("active");

    }


    if(this._bOptionPressed) {

        this.addClass("activeoption");
    
    }

},


/**
* @method _onMouseOut
* @description "mouseout" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onMouseOut: function(p_oEvent) {

    this.removeClass("hover");

    if(this.get("type") != "menubutton") {

        this.removeClass("active");

    }

    if(this._activationButtonPressed || this._bOptionPressed) {

        Event.on(document, "mouseup", this._onDocumentMouseUp, null, this);

    }
    
},


/**
* @method _onDocumentMouseUp
* @description "mouseup" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onDocumentMouseUp: function(p_oEvent) {

    this._activationButtonPressed = false;
    this._bOptionPressed = false;

    var sType = this.get("type");

    if(sType == "menubutton" || sType == "splitbutton") {

        this.removeClass((sType == "menubutton" ? "active" : "activeoption"));

        this._hideMenu();

    }

    Event.removeListener(document, "mouseup", this._onDocumentMouseUp);

},


/**
* @method _onMouseDown
* @description "mousedown" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onMouseDown: function(p_oEvent) {

    if((p_oEvent.which || p_oEvent.button) == 1) {

        if(!this.hasFocus()) {
        
            this.focus();
        
        }


        var sType = this.get("type");


        if(sType == "splitbutton") {
        
            var oElement = this.get("element"),
                nX = Event.getPageX(p_oEvent) - Dom.getX(oElement);

            if((oElement.offsetWidth - this.OPTION_AREA_WIDTH) < nX) {
                
                this.fireEvent("option", p_oEvent);

            }
            else {

                this.addClass("active");

                this._activationButtonPressed = true;

            }

        }
        else if(sType == "menubutton") {

            if(this.hasClass("active")) {

                this._hideMenu();

                this._activationButtonPressed = false;

            }
            else {

                this._showMenu(p_oEvent);

                this._activationButtonPressed = true;
            
            }

        }
        else {

            this.addClass("active");

            this._activationButtonPressed = true;
        
        }



        if(sType == "splitbutton" || sType == "menubutton") {

            var me = this;

            
            function onMouseUp() {
            
                me._hideMenu();
                me.removeListener("mouseup", onMouseUp);
            
            }


            this._hideMenuTimerId = window.setTimeout(function() {
            
                me.on("mouseup", onMouseUp);
            
            }, 250);

        }

    }
    
},


/**
* @method _onMouseUp
* @description "mouseup" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onMouseUp: function(p_oEvent) {

    if(this._hideMenuTimerId) {

        window.clearTimeout(this._hideMenuTimerId);

    }

    var sType = this.get("type");

    if(sType == "checkbox" || sType == "radio") {

        this.set("checked", !(this.get("checked")));
    
    }


    this._activationButtonPressed = false;
    

    if(this.get("type") != "menubutton") {

        this.removeClass("active");
    
    }
    
},


/**
* @method _onFocus
* @description "focus" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onFocus: function(p_oEvent) {

    this.addClass("focus");

    if(this._activationKeyPressed) {

        this.addClass("active");
   
    }

    m_oFocusedButton = this;


    if(!this._hasKeyEventHandlers) {

        var oElement = this._button;

        Event.on(oElement, "blur", this._onBlur, null, this);
        Event.on(oElement, "keydown", this._onKeyDown, null, this);
        Event.on(oElement, "keyup", this._onKeyUp, null, this);

        this._hasKeyEventHandlers = true;

    }


    this.fireEvent("focus", p_oEvent);

},


/**
* @method _onBlur
* @description "blur" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onBlur: function(p_oEvent) {

    this.removeClass("focus");

    if(this.get("type") != "menubutton") {

        this.removeClass("active");

    }    

    if(this._activationKeyPressed) {

        Event.on(document, "keyup", this._onDocumentKeyUp, null, this);

    }


    m_oFocusedButton = null;

    this.fireEvent("blur", p_oEvent);
   
},


/**
* @method _onDocumentKeyUp
* @description "keyup" event handler for the document.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onDocumentKeyUp: function(p_oEvent) {

    if(this._isActivationKey(Event.getCharCode(p_oEvent))) {

        this._activationKeyPressed = false;
        
        Event.removeListener(document, "keyup", this._onDocumentKeyUp);
    
    }

},


/**
* @method _onKeyDown
* @description "keydown" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onKeyDown: function(p_oEvent) {

    if(
        this.get("type") == "splitbutton" && 
        this._isSplitButtonOptionKey(p_oEvent)
    ) {

        this.fireEvent("option", p_oEvent);

    }
    else if(this._isActivationKey(Event.getCharCode(p_oEvent))) {

        if(this.get("type") == "menubutton") {

            this._showMenu(p_oEvent);

        }
        else {

            this._activationKeyPressed = true;
            
            this.addClass("active");
        
        }
    
    }


    var oMenu = this._menu;

    if(
        oMenu && oMenu.cfg.getProperty("visible") && 
        Event.getCharCode(p_oEvent) == 27
    ) {
    
        oMenu.hide();
        this.focus();
    
    }

},


/**
* @method _onKeyUp
* @description "keyup" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onKeyUp: function(p_oEvent) {

    if(this._isActivationKey(Event.getCharCode(p_oEvent))) {

        var sType = this.get("type");

        if(sType == "checkbox" || sType == "radio") {

            this.set("checked", !(this.get("checked")));
        
        }

        this._activationKeyPressed = false;

        if(this.get("type") != "menubutton") {

            this.removeClass("active");

        }

    }

},


/**
* @method _onClick
* @description "click" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onClick: function(p_oEvent) {

    var sType = this.get("type"),
        sTitle;

    switch(sType) {

        case "radio":
        case "checkbox":

            if(this.get("checked")) {
                
                sTitle = (sType == "radio") ? 
                            this.RADIO_CHECKED_TITLE : 
                            this.CHECKBOX_CHECKED_TITLE;
            
            }
            else {
            
                sTitle = (sType == "radio") ? 
                            this.RADIO_DEFAULT_TITLE : 
                            this.CHECKBOX_DEFAULT_TITLE;
            
            }
            
            this.set("title", sTitle);

        break;

        case "submit":

            this.submitForm();
        
        break;

        case "reset":

            var oForm = this.getForm();

            if(oForm) {

                oForm.reset();
            
            }

        break;

        case "menubutton":

            sTitle = this._menu.cfg.getProperty("visible") ? 
                            this.MENUBUTTON_MENU_VISIBLE_TITLE : 
                            this.MENUBUTTON_DEFAULT_TITLE;

            this.set("title", sTitle);

        break;

        case "splitbutton":

            var oElement = this.get("element"),
                nX = Event.getPageX(p_oEvent) - Dom.getX(oElement);

            if((oElement.offsetWidth - this.OPTION_AREA_WIDTH) < nX) {

                return false;
            
            }
            else {

                this._hideMenu();
    
                var oSrcElement = this.get("srcelement");
    
                if(oSrcElement && oSrcElement.type == "submit") {
    
                    this.submitForm();
                
                }
            
            }

            sTitle = this._menu.cfg.getProperty("visible") ? 
                            this.SPLITBUTTON_OPTION_VISIBLE_TITLE : 
                            this.SPLITBUTTON_DEFAULT_TITLE;

            this.set("title", sTitle);

        break;

    }

},


/**
* @method _onAppendTo
* @description "appendTo" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onAppendTo: function(p_oEvent) {

    /*
        It is necessary to call "getForm" using "setTimeout" to make sure that 
        the button's "form" property returns a node reference.  Sometimes, if
        you try to get the reference immediately after appending the field, it
        is null.
    */

    var me = this;

    window.setTimeout(function() {

        me._addListenersToForm();

    }, 0);

},


/**
* @method _onFormSubmit
* @description "submit" event handler for the button's form.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onFormSubmit: function(p_oEvent) {

    var sType = this.get("type"),
        oMenuItem = this.get("selectedMenuItem"),
        oForm = this.getForm();
    
    
    if(sType == "radio" || sType == "checkbox") {
    
        YAHOO.log("Creating hidden field for button: " + this);
    
        this.createHiddenField();
    
    }
    else if(oMenuItem) {
    
        var oSrcElement = this._menu.srcElement;

        if(oSrcElement && oSrcElement.tagName.toUpperCase() == "SELECT") {

            oForm.appendChild(oSrcElement);
            oSrcElement.selectedIndex = oMenuItem.index;

        }
        else {
    
            var oValue = (oMenuItem.value === null || oMenuItem.value === "") ? 
                                oMenuItem.cfg.getProperty("text") : 
                                oMenuItem.value;


            if(oValue) {
    
                var oField = createInputElement(
                                    "hidden", 
                                    (this.get("name") + "_options"),
                                    oValue
                                );
    
                oForm.appendChild(oField);
    
            }
    
        }                
    
    }

},


/**
* @method _onFormReset
* @description "reset" event handler for the button's form.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onFormReset: function(p_oEvent) {

    var sType = this.get("type");

    if(sType == "checkbox" || sType == "radio") {

        this.resetValue("checked");

    }

    if(this._menu) {

        this.resetValue("selectedMenuItem");

    }

},


/**
* @method _onDocumentMouseDown
* @description "mousedown" event handler for the document.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onDocumentMouseDown: function(p_oEvent) {

    var oTarget = Event.getTarget(p_oEvent),
        oButtonElement = this.get("element"),
        oMenuElement = this._menu.element;

    if(
        oTarget != oButtonElement && 
        !Dom.isAncestor(oButtonElement, oTarget) && 
        oTarget != oMenuElement && 
        !Dom.isAncestor(oMenuElement, oTarget)
    ) {

        this._hideMenu();

        Event.removeListener(
                document, 
                "mousedown", 
                this._onDocumentMouseDown
            );    
    
    }

},


/**
* @method _onOption
* @description "option" event handler for the button.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onOption: function(p_oEvent) {

    if(this.hasClass("activeoption")) {

        this._hideMenu();

        this._bOptionPressed = false;

    }
    else {

        this._showMenu(p_oEvent);    

        this._bOptionPressed = true;

    }

},


/**
* @method _onMenuShow
* @description "show" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuShow: function(p_sType, p_aArgs) {

    Event.on(document, "mousedown", this._onDocumentMouseDown, null, this);

    var sTitle,
        sClass;
    
    if(this.get("type") == "splitbutton") {

        sTitle = this.SPLITBUTTON_OPTION_VISIBLE_TITLE;
        sClass = "activeoption";
    
    }
    else {

        sTitle = this.MENUBUTTON_MENU_VISIBLE_TITLE;        
        sClass = "active";

    }

    this.addClass(sClass);
    this.set("title", sTitle);

},


/**
* @method _onMenuHide
* @description "hide" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuHide: function(p_sType, p_aArgs) {
    
    if(this._originalMaxHeight != -1) {
    
        this._menu.cfg.setProperty("maxheight", this._originalMaxHeight);

    }


    var sTitle,
        sClass;
    
    if(this.get("type") == "splitbutton") {

        sTitle = this.SPLITBUTTON_DEFAULT_TITLE;
        sClass = "activeoption";

    }
    else {

        sTitle = this.MENUBUTTON_DEFAULT_TITLE;        
        sClass = "active";
    }


    this.removeClass(sClass);
    this.set("title", sTitle);


    if(this.get("type") == "splitbutton") {

        this._bOptionPressed = false;
    
    }

},


/**
* @method _onMenuKeyDown
* @description "keydown" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuKeyDown: function(p_sType, p_aArgs) {

    var oEvent = p_aArgs[0];

    if(Event.getCharCode(oEvent) == 27) {

        this.focus();

        if(this.get("type") == "splitbutton") {
        
            this._bOptionPressed = false;
        
        }

    }

},


/**
* @method _onMenuRender
* @description "render" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuRender: function(p_sType, p_aArgs) {

    this.get("element").parentNode.appendChild(this._menu.element);

},


/**
* @method _onMenuItemSelected
* @description "selectedchange" event handler for each item in the 
* button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {<a href="YAHOO.widget.MenuItem.html">YAHOO.widget.MenuItem</a>} 
* p_oItem Object representing the menu item that subscribed to the event.
*/
_onMenuItemSelected: function(p_sType, p_aArgs, p_oItem) {
    
    this.set("selectedMenuItem", p_oItem);

},


/**
* @method _onMenuItemAdded
* @description "itemadded" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
* @param {<a href="YAHOO.widget.MenuItem.html">YAHOO.widget.MenuItem</a>} 
* p_oItem Object representing the menu item that subscribed to the event.
*/
_onMenuItemAdded: function(p_sType, p_aArgs, p_oItem) {
    
    var oItem = p_aArgs[0];

    oItem.cfg.subscribeToConfigEvent(
                "selected", 
                this._onMenuItemSelected, 
                oItem, 
                this
            );

},


/**
* @method _onMenuClick
* @description "click" event handler for the button's menu.
* @private
* @param {String} p_sType String representing the name of the event that 
* was fired.
* @param {Array} p_aArgs Array of arguments sent when the event was fired.
*/
_onMenuClick: function(p_sType, p_aArgs) {

    var oItem = p_aArgs[1];

    if(oItem) {

        var oSrcElement = this.get("srcelement");
    
        if(oSrcElement && oSrcElement.type == "submit") {
    
            this.submitForm();
    
        }
    
        this._hideMenu();
    
    }

},



// Public methods


/**
* @method createHiddenField
* @description Creates the button's hidden form field and appends it to its
* parent form.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>}
*/
createHiddenField: function () {

    if (!this.get("disabled")) {

        var sType = this.get("type"),
        
            bCheckable = (sType == "checkbox" || sType == "radio"),
        
            oField = createInputElement(
            
                        /*
                            Use "submit" type for IE so that the input 
                            element will be able to be clicked via a call to 
                            the "click" method by the "submitForm" method.
                        */
            
                        (bCheckable ? sType : (m_bIE ? "submit" : "hidden")),
                        this.get("name"),
                        this.get("value"),
                        this.get("checked")
                    ),
                    
            oForm = this.getForm();
    

        if (oField) {

            if (bCheckable || oField.type == "submit") {

                oField.style.display = "none";

            }


            if (oForm) {
        
                var oHiddenField = this._hiddenField;
        
                if (oHiddenField && Dom.inDocument(oHiddenField)) {
        
                    oForm.replaceChild(oField, oHiddenField);
        
                }
                else {
        
                    oForm.appendChild(oField);
                    
                }
            
            }
    
            this._hiddenField = oField;
    
            return oField;

        }

    }

},


/**
* @method submitForm
* @description Submits the form to which the button belongs.
* @protected
*/
submitForm: function(p_oMenuItem) {

    var oForm = this.getForm();

    if (oForm) {

        var oInput = this.createHiddenField();

        if (m_bIE) {

            /*
                Clicking the button via a call to the "click" method will 
                cause IE to both fire the form's "submit" event as well as 
                submit the form.  Originally tried just firing the "submit"
                event via "fireEvent," but then the event could not 
                be cancelled.
            */            

            oInput.click();

        }
        else {  // Gecko, Opera, and Safari

            var oEvent = document.createEvent("HTMLEvents");
            oEvent.initEvent("submit", true, true);

            /*
                In Safari, dispatching a "submit" event to a form WILL cause  
                the form's "submit" event to fire, but WILL NOT submit the   
                form.  Therefore, we need to call the "submit" method as well.
            */

            var bSubmitForm = oForm.dispatchEvent(oEvent);
          
            if(m_bSafari && bSubmitForm) {

                oForm.submit();
            
            }

        }
    
    }

},


/**
* @method init
* @description The Button class's initialization method.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;input&#62;</code>, <code>&#60;a&#62;</code> or 
* <code>&#60;span&#62;</code> element to be used to create the button.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>|<a href="
* http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#
* ID-33759296">HTMLElement</a>} p_oElement Object reference for the 
* <code>&#60;input&#62;</code>, <code>&#60;a&#62;</code> or 
* <code>&#60;span&#62;</code> element to be used to create the button.
* @param {Object} p_oElement Object literal specifying a set of configuration 
* attributes used to create the button.
* @param {Object} p_oAttributes Optional. Object literal specifying a set of  
* configuration attributes used to create the button.
*/
init: function(p_oElement, p_oAttributes) {

    var sTagName = p_oAttributes.type == "link" ? "A" : "BUTTON",
        oSrcElement = p_oAttributes.srcelement;


    this._button = p_oElement.getElementsByTagName(sTagName)[0];


    YAHOO.widget.Button.superclass.init.call(
            this, p_oElement, 
            p_oAttributes
        );


    m_oButtons[this.get("id")] = this;


    this.addClass(this.CSS_CLASS_NAME);
    
    if(m_bIE && !m_bIE7) {

        this.addClass("ie6");

    }
    
    this.addClass(this.get("type"));

    Event.on(this._button, "focus", this._onFocus, null, this);
    this.on("mouseover", this._onMouseOver);
    this.on("click", this._onClick);
    this.on("appendTo", this._onAppendTo);
    
    var oContainer = this.get("container"),
        oElement = this.get("element");


    if(oContainer) {

        if(Lang.isString(oContainer)) {

            var me = this;

            Event.onContentReady(oContainer, function() {

                me.appendTo(this);            
            
            });

        }
        else {

            this.appendTo(oContainer);

        }

    }
    else if(
        !Dom.inDocument(oElement) && 
        oSrcElement && 
        oSrcElement.tagName.toUpperCase() == "INPUT"
    ) {

        var oParentNode = oSrcElement.parentNode;

        if(oParentNode) {

            this.fireEvent("beforeAppendTo", {
                type: "beforeAppendTo",
                target: oParentNode
            });
    
            oParentNode.replaceChild(oElement, oSrcElement);
    
            this.fireEvent("appendTo", {
                type: "appendTo",
                target: oParentNode
            });
        
        }

    }
    else if(
        Dom.inDocument(oElement) && 
        oSrcElement.tagName.toUpperCase() == "SPAN"
    ) {

        this._addListenersToForm();

    }

    this.logger.log("Initialization completed.");

},


/**
* @method initAttributes
* @description Initializes all of the configuration attributes used to create 
* the button.
* @param {Object} p_oAttributes Object literal specifying a set of 
* configuration attributes used to create the button.
*/
initAttributes: function(p_oAttributes) {

    var oAttributes = p_oAttributes || {};

    YAHOO.widget.Button.superclass.initAttributes.call(this, oAttributes);


    /**
    * @config type
    * @description String specifying the button's type.  Possible values are: 
    * "button," "link," "submit," "reset," "checkbox," "radio," "menubutton," 
    * and "splitbutton."
    * @default "button"
    * @type String
    */
    this.setAttributeConfig("type", {

        value: (oAttributes.type || "button"),
        validator: Lang.isString,
        writeOnce: true,
        method: this._setType

    });


    /**
    * @config label
    * @description String specifying the button's text label or innerHTML.
    * @default null
    * @type String
    */
    this.setAttributeConfig("label", {

        value: oAttributes.label,
        validator: Lang.isString,
        method: this._setLabel

    });


    /**
    * @config value
    * @description Object specifying the value for the button.
    * @default null
    * @type Object
    */
    this.setAttributeConfig("value", {

        value: oAttributes.value

    });


    /**
    * @config name
    * @description String specifying the name for the button.
    * @default null
    * @type String
    */
    this.setAttributeConfig("name", {

        value: oAttributes.name,
        validator: Lang.isString

    });


    /**
    * @config tabindex
    * @description Number specifying the tabindex for the button.
    * @default null
    * @type Number
    */
    this.setAttributeConfig("tabindex", {

        value: oAttributes.tabindex,
        validator: Lang.isNumber,
        method: this._setTabIndex

    });


    /**
    * @config title
    * @description String specifying the title for the button.
    * @default null
    * @type String
    */
    this.configureAttribute("title", {

        value: oAttributes.title,
        validator: Lang.isString,
        method: this._setTitle

    });


    /**
    * @config disabled
    * @description Boolean indicating if the button should be disabled.  
    * (Disabled buttons are dimmed and will not respond to user input 
    * or fire events.  Does not apply to button's of type "link.")
    * @default false
    * @type Boolean
    */
    this.setAttributeConfig("disabled", {

        value: (oAttributes.disabled || false),
        validator: Lang.isBoolean,
        method: this._setDisabled

    });


    /**
    * @config href
    * @description String specifying the href for the button.  Applies only to 
    * buttons of type "link."
    * @type String
    */
    this.setAttributeConfig("href", {

        value: oAttributes.href,
        validator: Lang.isString,
        method: this._setHref

    });


    /**
    * @config target
    * @description String specifying the target for the button.  Applies only  
    * to buttons of type "link."
    * @type String
    */
    this.setAttributeConfig("target", {

        value: oAttributes.target,
        validator: Lang.isString,
        method: this._setTarget

    });


    /**
    * @config checked
    * @description Boolean indicating if the button is checked.  Applies only
    * to buttons of type "radio" and "checkbox."
    * @default false
    * @type Boolean
    */
    this.setAttributeConfig("checked", {

        value: (oAttributes.checked || false),
        validator: Lang.isBoolean,
        method: this._setChecked

    });


	/**
	* @config container
	* @description HTML element reference or string specifying the id 
	* attribute of the HTML element that the button's markup should be 
	* rendered into.
	* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
	* level-one-html.html#ID-58190037">HTMLElement</a>|String
	* @default null
	*/
    this.setAttributeConfig("container", {

        value: oAttributes.container

    });


	/**
	* @config srcelement
    * @description Object reference to the HTML element (either 
    * <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code>) used to 
    * create the button.
	* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
	* level-one-html.html#ID-58190037">HTMLElement</a>|String
	* @default null
	*/
    this.setAttributeConfig("srcelement", {

        value: oAttributes.srcelement,
        writeOnce: true

    });


	/**
	* @config menu
    * @description Object specifying the menu for the button.  The value can be
    * one of the following:
    * <ul>
    * <li>Object specifying a <a href="YAHOO.widget.Menu.html">
    * YAHOO.widget.Menu</a> instance.</li>
    * <li>String specifying the id attribute of the <code>&#60;div&#62;</code> 
    * element used to create the menu.</li>
    * <li>String specifying the id attribute of the 
    * <code>&#60;select&#62;</code> element used to create the menu.</li>
    * <li>Object specifying the <code>&#60;div&#62;</code> element used to 
    * create the menu.</li>
    * <li>Object specifying the <code>&#60;select&#62;</code> element used to 
    * create the menu.</li>
    * <li>Array of object literals, each representing a set of 
    * <a href="YAHOO.widget.MenuItem.html">YAHOO.widget.MenuItem</a> 
    * configuration attributes.</li>
    * <li>Array of strings representing the text labels for each menu item in 
    * the menu.</li>
    * </ul>
	* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
	* level-one-html.html#ID-58190037">HTMLElement</a>|String|Array
	* @default null
	*/
    this.setAttributeConfig("menu", {

        value: null,
        method: this._setMenu
    
    });


	/**
	* @config selectedMenuItem
    * @description Reference to the item in the button's menu that is
    * currently selected.
	* @type <a href="YAHOO.widget.MenuItem.html">YAHOO.widget.MenuItem</a>
	* @default null
	*/
    this.setAttributeConfig("selectedMenuItem", {

        value: null
    
    });


	/**
	* @config onclick
    * @description Object literal representing the code to be executed when 
    * the button is clicked.  Format:<br> <code> {<br> 
    * <strong>fn:</strong> Function,   &#47;&#47; The handler to call when the 
    * event fires.<br> <strong>obj:</strong> Object, &#47;&#47; An object to 
    * pass back to the handler.<br> <strong>scope:</strong> Object &#47;&#47; 
    * The object to use for the scope of the handler.<br> } </code>
    * @type Object
	* @default null
	*/
    this.setAttributeConfig("onclick", {

        value: oAttributes.onclick,
        method: this._setOnClick
    
    });

},


/**
* @method focus
* @description Causes the button to receive the focus and fires the button's
* "focus" event.
*/
focus: function() {

    if(!this.get("disabled")) {

        this._button.focus();
    
    }

},


/**
* @method blur
* @description Causes the button to lose focus and fires the button's
* "blur" event.
*/
blur: function() {

    if(!this.get("disabled")) {

        this._button.blur();

    }

},


/**
* @method hasFocus
* @description Returns a boolean indicating whether or not the button has focus.
* @return {Boolean}
*/
hasFocus: function() {

    return (m_oFocusedButton == this);

},


/**
* @method isActive
* @description Returns a boolean indicating whether or not the button is active.
* @return {Boolean}
*/
isActive: function() {

    return this.hasClass("active");

},


/**
* @method getMenu
* @description Returns a reference to the button's menu.
* @return {<a href="YAHOO.widget.Menu.html">YAHOO.widget.Menu</a>}
*/
getMenu: function() {

    return this._menu;

},


/**
* @method getForm
* @description Returns a reference to the button's parent form.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-40002357">HTMLFormElement</a>}
*/
getForm: function() {

    return this._button.form;

},


/** 
* @method getHiddenField
* @description Returns a reference to the <code>&#60;input&#62;</code> element 
* used when the button's parent form is submitted.
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>}
*/
getHiddenField: function() {

    return this._hiddenField;

},


/**
* @method destroy
* @description Removes the button's element from its parent element and 
* removes all event handlers.
*/
destroy: function() {

    this.logger.log("Destroying ...");

    var oElement = this.get("element"),
        oParentNode = oElement.parentNode,
        oMenu = this._menu;

    if(oMenu) {

        this.logger.log("Destroying menu.");

        oMenu.destroy();

    }

    this.logger.log("Removing DOM event handlers.");

    Event.purgeElement(oElement);
    Event.purgeElement(this._button);
    Event.removeListener(document, "mouseup", this._onDocumentMouseUp);
    Event.removeListener(document, "keyup", this._onDocumentKeyUp);
    Event.removeListener(document, "mousedown", this._onDocumentMouseDown);


    var oForm = this.getForm();
    
    if(oForm) {

        Event.removeListener(oForm, "reset", this._onFormReset);
        Event.removeListener(oForm, "submit", this._onFormSubmit);

    }


    oParentNode.removeChild(oElement);

    this.logger.log("Removing from document.");

    delete m_oButtons[this.get("id")];

    this.logger.log("Destroyed.");

},


fireEvent: function(p_sType , p_aArgs) {

    //  Disabled buttons should not respond to DOM events

    if(this.DOM_EVENTS[p_sType] && this.get("disabled")) {

        return;

    }

    YAHOO.widget.Button.superclass.fireEvent.call(this, p_sType, p_aArgs);

},


/**
* @method toString
* @description Returns a string representing the button.
* @return {String}
*/
toString: function() {

    return ("Button " + this.get("id"));

}

});


/**
* @method onFormKeyDown
* @description "keydown" event handler for the button's form.
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
YAHOO.widget.Button.onFormKeyDown = function(p_oEvent) {

    var oTarget = Event.getTarget(p_oEvent),
        nCharCode = Event.getCharCode(p_oEvent);


    if (
        nCharCode == 13 && 
        oTarget.tagName && 
        oTarget.tagName.toUpperCase() == "INPUT"
    ) {

        var sType = oTarget.type;


        if(
            sType == "text" || sType == "password" || sType == "checkbox" || 
            sType == "radio" || sType == "file"
        ) {


            function isYUISubmitButton(p_oElement) {
    
                var sId = p_oElement.id;
    
                if (sId) {
    
                    var oButton = m_oButtons[sId];
        
                    if (oButton) {
        
                        var oSrcElement = oButton.get("srcelement");
        
                        return (
                                    oButton.get("type") == "submit" || 
                                    (
                                        oSrcElement && 
                                        oSrcElement.type == "submit"
                                    )
                                );
        
                    }
                
                }
            
            }
    
    
            var aButtons = Dom.getElementsBy(
                                isYUISubmitButton,
                                this.TAG_NAME, 
                                this.getForm()
                            ),
    
                nButtons = aButtons.length;
    
    
            if (nButtons > 0) {
    
                m_oButtons[aButtons[0].id].submitForm();
            
            }

        
        }

    }

};


/**
* @method addHiddenFieldsToForm
* @description Searches the specified form and adds hidden fields for instances 
* of YAHOO.widget.Button that are of type "radio," "checkbox," "menubutton," 
* and "splitbutton."
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-40002357">HTMLFormElement</a>} p_oForm Object reference 
* for the form to search.
*/
YAHOO.widget.Button.addHiddenFieldsToForm = function(p_oForm) {

    var aButtons = Dom.getElementsByClassName("yuibutton", "*", p_oForm),
        nButtons = aButtons.length;


    if(nButtons > 0) {

        YAHOO.log("Form contains " + nButtons + " YUI buttons.");

        var oButton = null,
            sType = null,
            oMenuItem = null,
            oMenu = null;

        for(var i=0; i<nButtons; i++) {

            oButton = m_oButtons[aButtons[i].id];

            if(oButton) {

                sType = oButton.get("type");
                oMenuItem = oButton.get("selectedMenuItem");


                if(sType == "radio" || sType == "checkbox") {
    
                    YAHOO.log("Creating hidden field for button: " + oButton);
    
                    oButton.createHiddenField();
                
                }
                else if(oMenuItem) {
    
                    oMenu = oButton.getMenu();
    
                    var oSrcElement = oMenu.srcElement;
                        
                    if(
                        oSrcElement && 
                        oSrcElement.tagName.toUpperCase() == "SELECT"
                    ) {
            
                        p_oForm.appendChild(oSrcElement);
                        oSrcElement.selectedIndex = oMenuItem.index;
            
                    }
                    else {
    
                        var oValue = (
                                        oMenuItem.value === null || 
                                        oMenuItem.value === ""
                                    ) ? oMenuItem.cfg.getProperty("text") : 
                                    oMenuItem.value;

                        if(oValue) {

                            var oHiddenField = oButton.getHiddenField(),
    
                                oField = createInputElement(
                                            "hidden",
                                            (oButton.get("name") + "_options"),
                                            oValue
                                        );
                                        
                            if(oHiddenField && Dom.inDocument(oHiddenField)) {

                                p_oForm.replaceChild(oField, oHiddenField);

                            }
                            else {

                                p_oForm.appendChild(oField);
                            
                            }
    
                        }
    
                    }                
    
                }
                
            }
        
        }

    }

};



// Events


/**
* @event focus
* @description Fires when the menu item receives focus.  Passes back a single 
* object representing the original DOM event object passed back by the event 
* utility (YAHOO.util.Event) when the event was fired.  See <a href="
* YAHOO.util.Element.html#addListener">Element.addListener</a> for more 
* information on listening for this event.
* @type YAHOO.util.CustomEvent
*/


/**
* @event blur
* @description Fires when the menu item loses the input focus.  Passes back a 
* single object representing the original DOM event object passed back by the 
* event utility (YAHOO.util.Event) when the event was fired.  See <a href="
* YAHOO.util.Element.html#addListener">Element.addListener</a> for more 
* information on listening for this event.
* @type YAHOO.util.CustomEvent
*/


/**
* @event option
* @description Fires when the user invokes the button's option.  Passes back a 
* single object representing the original DOM event (either "mousedown" or 
* "keydown") that caused the "option" event to fire.  See <a href="
* YAHOO.util.Element.html#addListener">Element.addListener</a> for more 
* information on listening for this event.
* @type YAHOO.util.CustomEvent
*/

})();
(function() {

// Shorthard for utilities

var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    Lang = YAHOO.lang,
    Button = YAHOO.widget.Button;    

    // Private collection of radio buttons

    m_oButtons = {};



/**
* The ButtonGroup class creates a set of buttons that are mutually exclusive; 
* checking one button in the set will uncheck all others in the button group.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the button group.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the button group.
* @param {Object} p_oElement Object literal specifying a set of 
* configuration attributes used to create the button group.
* @param {Object} p_oAttributes Optional. Object literal specifying a set of 
* configuration attributes used to create the button group.
* @namespace YAHOO.widget
* @class ButtonGroup
* @constructor
* @extends YAHOO.util.Element
*/
YAHOO.widget.ButtonGroup = function(p_oElement, p_oAttributes) {

    var fnSuperClass = YAHOO.widget.ButtonGroup.superclass.constructor;

    if(
        arguments.length == 1 && 
        !Lang.isString(p_oElement) && 
        !p_oElement.nodeName
    ) {

        if(!p_oElement.id) {

            var sId = Dom.generateId();

            p_oElement.id = sId;

            YAHOO.log(
                "No value specified for the button group's \"id\" attribute. " +
                "Setting button group id to \"" + sId + "\".",
                "warn"
            );

        }

        this.logger = new YAHOO.widget.LogWriter("ButtonGroup " + sId);

        this.logger.log(
                "No source HTML element.  "  +
                "Building the button group using the set of " + 
                "configuration attributes."
            );

        fnSuperClass.call(this, (this._createGroupElement()), p_oElement);

    }
    else if(Lang.isString(p_oElement)) {

        var oElement = Dom.get(p_oElement);

        if (oElement) {
        
            if(oElement.nodeName.toUpperCase() == this.TAG_NAME) {

                this.logger = 
                    new YAHOO.widget.LogWriter("ButtonGroup " + p_oElement);
        
                fnSuperClass.call(this, oElement, p_oAttributes);

            }

        }
    
    }
    else {

        var sNodeName = p_oElement.nodeName;

        if(sNodeName && sNodeName == this.TAG_NAME) {
    
            if(!p_oElement.id) {
    
                p_oElement.id = Dom.generateId();
    
                YAHOO.log(
                    "No value specified for the button group's \"id\" " +
                    "attribute. Setting button group id " +
                    "to \"" + p_oElement.id + "\".",
                    "warn"
                );
    
            }
    
            this.logger = 
                    new YAHOO.widget.LogWriter("ButtonGroup " + p_oElement.id);
    
            fnSuperClass.call(this, p_oElement, p_oAttributes);

        }

    }

};


YAHOO.extend(YAHOO.widget.ButtonGroup, YAHOO.util.Element, {


// Protected properties


/** 
* @property _buttons
* @description Array of buttons in the button group.
* @default null
* @protected
* @type Array
*/
_buttons: null,



// Constants


/**
* @property TAG_NAME
* @description The name of the tag to be used for the button group's element. 
* @default "DIV"
* @final
* @type String
*/
TAG_NAME: "DIV",


/**
* @property CSS_CLASS_NAME
* @description String representing the CSS class(es) to be applied to the 
* button group's element.
* @default "yuibuttongroup"
* @final
* @type String
*/
CSS_CLASS_NAME: "yuibuttongroup",



// Protected methods


/**
* @method _createGroupElement
* @description Creates the button group's element.
* @protected
* @return {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>}
*/
_createGroupElement: function() {

    var oElement = document.createElement(this.TAG_NAME);

    oElement.className = this.CSS_CLASS_NAME;

    return oElement;

},



// Protected attribute setter methods


/**
* @method _setDisabled
* @description Sets the value of the button groups's "disabled" attribute.
* @protected
* @param {Boolean} p_bDisabled Boolean indicating the value for the button
* group's "disabled" attribute.
*/
_setDisabled: function(p_bDisabled) {

    var nButtons = this.getCount();

    if(nButtons > 0) {

        var i = nButtons - 1;
        
        do {

            this._buttons[i].set("disabled", p_bDisabled);
        
        }
        while(i--);

    }

},



// Protected event handlers


/**
* @method _onKeyDown
* @description "keydown" event handler for the button group.
* @protected
* @param {Event} p_oEvent Object representing the DOM event object passed 
* back by the event utility (YAHOO.util.Event).
*/
_onKeyDown: function(p_oEvent) {

    var oTarget = Event.getTarget(p_oEvent),
        nCharCode = Event.getCharCode(p_oEvent),
        sId = oTarget.parentNode.parentNode.id,
        oButton = m_oButtons[sId],
        nIndex = -1;


    if(nCharCode == 37 || nCharCode == 38) {

        nIndex = (oButton.index === 0) ? 
                    (this._buttons.length -1) : (oButton.index - 1);
    
    }
    else if(nCharCode == 39 || nCharCode == 40) {

        nIndex = (oButton.index === (this._buttons.length - 1)) ? 
                    0 : (oButton.index + 1);

    }


    if(nIndex > -1) {

        this.check(nIndex);
        this.getButton(nIndex).focus();
    
    }        

},


/**
* @method _onAppendTo
* @description "appendTo" event handler for the button group.
* @protected
* @param {Event} p_oEvent Object representing the event that was fired.
*/
_onAppendTo: function(p_oEvent) {

    var aButtons = this._buttons,
        nButtons = aButtons.length;

    for(var i=0; i<nButtons; i++) {

        aButtons[i].appendTo(this.get("element"));

    }

},


/**
* @method _onButtonCheckedChange
* @description "checkedChange" event handler for each button in the 
* button group.
* @protected
* @param {Event} p_oEvent Object representing the event that was fired.
* @param {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>} p_oButton 
* Object representing the button that fired the event.
*/
_onButtonCheckedChange: function(p_oEvent, p_oButton) {

    var bChecked = p_oEvent.newValue,
        oCheckedButton = this.get("checkedButton");

    if(bChecked && oCheckedButton != p_oButton) {

        if(oCheckedButton) {

            oCheckedButton.set("checked", false, true);

        }

        this.set("checkedButton", p_oButton);
        this.set("value", p_oButton.get("value"));

    }
    else if(oCheckedButton && !oCheckedButton.set("checked")) {

        oCheckedButton.set("checked", true, true);

    }
   
},



// Public methods


/**
* @method init
* @description The ButtonGroup class's initialization method.
* @param {String} p_oElement String specifying the id attribute of the 
* <code>&#60;div&#62;</code> element of the button group.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
* level-one-html.html#ID-22445964">HTMLDivElement</a>} p_oElement Object 
* specifying the <code>&#60;div&#62;</code> element of the button group.
* @param {Object} p_oElement Object literal specifying a set of configuration 
* attributes used to create the button group.
* @param {Object} p_oAttributes Optional. Object literal specifying a set of 
* configuration attributes used to create the button group.
*/
init: function(p_oElement, p_oAttributes) {

    this._buttons = [];

    YAHOO.widget.ButtonGroup.superclass.init.call(
            this, p_oElement, 
            p_oAttributes
        );


    this.logger.log(
        "Searching for child nodes with the class name " +
        "\"yuibutton\" to add to the button group."
    );

    var aButtons = this.getElementsByClassName("yuibutton");


    if(aButtons.length > 0) {

        this.logger.log(
            "Found " + aButtons.length + " child nodes with the class " + 
            "name \"yuibutton.\"  Attempting to add to button group."
        );

        this.addButtons(aButtons);

    }


    this.logger.log(
        "Searching for child nodes with the type of \"radio\" to add to the " +
        " button group."
    );

    function isRadioButton(p_oElement) {

        return (p_oElement.type == "radio");

    }

    aButtons = Dom.getElementsBy(isRadioButton, "input", this.get("element"));


    if(aButtons.length > 0) {

        this.logger.log(
            "Found " + aButtons.length + " child nodes with the type of " +
            "\"radio.\"  Attempting to add to button group."
        );

        this.addButtons(aButtons);

    }

    this.on("keydown", this._onKeyDown);
    this.on("appendTo", this._onAppendTo);

    var oContainer = this.get("container");

    if(oContainer) {

        if(Lang.isString(oContainer)) {

            var me = this;

            Event.onContentReady(oContainer, function() {

                me.appendTo(this);            
            
            });

        }
        else {

            this.appendTo(oContainer);

        }

    }


    this.logger.log("Initialization completed.");

},


/**
* @method initAttributes
* @description Initializes all of the configuration attributes used to create  
* the button group.
* @param {Object} p_oAttributes Object literal specifying a set of 
* configuration attributes used to create the button group.
*/
initAttributes: function(p_oAttributes) {

    var oAttributes = p_oAttributes || {};

    YAHOO.widget.ButtonGroup.superclass.initAttributes.call(
        this, 
        oAttributes
    );


    /**
    * @config name
    * @description String specifying the name for the button group.  This
    * name will be applied to each button in the button group.
    * @default null
    * @type String
    */
    this.setAttributeConfig("name", {

        value: oAttributes.name,
        validator: Lang.isString

    });


    /**
    * @config disabled
    * @description Boolean indicating if the button group should be disabled.  
    * Disabling the button group will disable each button in the button group.  
    * Disabled buttons are dimmed and will not respond to user input 
    * or fire events.
    * @default false
    * @type Boolean
    */
    this.setAttributeConfig("disabled", {

        value: (oAttributes.disabled || false),
        validator: Lang.isBoolean,
        method: this._setDisabled

    });


    /**
    * @config value
    * @description Object specifying the value for the button group.
    * @default null
    * @type Object
    */
    this.setAttributeConfig("value", {

        value: oAttributes.value

    });


	/**
	* @config container
	* @description HTML element reference or string specifying the id 
	* attribute of the HTML element that the button group's markup should be 
	* rendered into.
	* @type <a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/
	* level-one-html.html#ID-58190037">HTMLElement</a>|String
	* @default null
	*/
    this.setAttributeConfig("container", {

        value: oAttributes.container

    });


	/**
	* @config checkedButton
    * @description Reference for the button in the button group that 
    * is checked.
	* @type {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>}
	* @default null
	*/
    this.setAttributeConfig("checkedButton", {

        value: null

    });

},


/**
* @method addButton
* @description Adds the button to the button group.
* @param {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>} p_oButton 
* Object reference for the <a href="YAHOO.widget.Button.html">
* YAHOO.widget.Button</a> instance to be added to the button group.
* @param {String} p_oButton String specifying the id attribute of the 
* <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code> element to be 
* used to create the button to be added to the button group.
* @param {<a href="http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-
* one-html.html#ID-6043025">HTMLInputElement</a>|<a href="
* http://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-html.html#
* ID-33759296">HTMLElement</a>} p_oButton Object reference for the 
* <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code> element to be 
* used to create the button to be added to the button group.
* @param {Object} p_oButton Object literal specifying a set of 
* <a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a> configuration 
* attributes used to configure the button to be added to the button group.
* @return {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>} 
*/
addButton: function(p_oButton) {

    var oButton;


    if(p_oButton instanceof Button && p_oButton.get("type") == "radio") {

        oButton = p_oButton;

    }
    else if(!Lang.isString(p_oButton) && !p_oButton.nodeName) {

        p_oButton.type = "radio";

        oButton = new Button(p_oButton);
    
    }
    else {

        oButton = new Button(p_oButton, { type: "radio" });

    }


    if(oButton) {

        var nIndex = this._buttons.length,
            sButtonName = oButton.get("name"),
            sGroupName = this.get("name");

        oButton.index = nIndex;

        this._buttons[nIndex] = oButton;
        m_oButtons[oButton.get("id")] = oButton;


        if(sButtonName != sGroupName) {

            oButton.set("name", sGroupName);
        
        }


        if(this.get("disabled")) {

            oButton.set("disabled", true);

        }


        if(oButton.get("checked")) {

            this.set("checkedButton", oButton);

        }

        
        oButton.on("checkedChange", this._onButtonCheckedChange, oButton, this);

        this.logger.log("Button " + oButton.get("id") + " added.");

        return oButton;

    }

},


/**
* @method addButtons
* @description Adds the array of buttons to the button group.
* @param {Array} p_aButtons Array of <a href="YAHOO.widget.Button.html">
* YAHOO.widget.Button</a> instances to be added 
* to the button group.
* @param {Array} p_aButtons Array of strings specifying the id attribute of 
* the <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code> elements to 
* be used to create the buttons to be added to the button group.
* @param {Array} p_aButtons Array of object references for the 
* <code>&#60;input&#62;</code> or <code>&#60;span&#62;</code> elements to be 
* used to create the buttons to be added to the button group.
* @param {Array} p_aButtons Array of object literals, each containing a set of 
* <a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a> configuration 
* attributes used to configure each button to be added to the button group.
* @return {Array}
*/
addButtons: function(p_aButtons) {

    if(Lang.isArray(p_aButtons)) {
    
        var nButtons = p_aButtons.length,
            oButton,
            aButtons = [];

        if(nButtons > 0) {

            for(var i=0; i<nButtons; i++) {

                oButton = this.addButton(p_aButtons[i]);
                
                if(oButton) {

                    aButtons[aButtons.length] = oButton;

                }
            
            }

            if(aButtons.length > 0) {

                this.logger.log(aButtons.length + " buttons added.");

                return aButtons;

            }
        
        }

    }

},


/**
* @method removeButton
* @description Removes the button at the specified index from the button group.
* @param {Number} p_nIndex Number specifying the index of the button to be 
* removed from the button group.
*/
removeButton: function(p_nIndex) {

    var oButton = this.getButton(p_nIndex);
    
    if(oButton) {

        this.logger.log("Removing button " + oButton.get("id") + ".");

        this._buttons.splice(p_nIndex, 1);
        delete m_oButtons[oButton.get("id")];

        oButton.removeListener("checkedChange", this._onButtonCheckedChange);
        oButton.destroy();


        var nButtons = this._buttons.length;
        
        if(nButtons > 0) {

            var i = this._buttons.length - 1;
            
            do {

                this._buttons[i].index = i;

            }
            while(i--);
        
        }

        this.logger.log("Button " + oButton.get("id") + " removed.");

    }

},


/**
* @method getButton
* @description Returns the button at the specified index.
* @param {Number} p_nIndex The index of the button to retrieve from the 
* button group.
* @return {<a href="YAHOO.widget.Button.html">YAHOO.widget.Button</a>}
*/
getButton: function(p_nIndex) {

    if(Lang.isNumber(p_nIndex)) {

        return this._buttons[p_nIndex];

    }

},


/**
* @method getButtons
* @description Returns an array of the buttons in the button group.
* @return {Array}
*/
getButtons: function() {

    return this._buttons;

},


/**
* @method getCount
* @description Returns the number of buttons in the button group.
* @return {Number}
*/
getCount: function() {

    return this._buttons.length;

},


/**
* @method focus
* @description Sets focus to the button at the specified index.
* @param {Number} p_nIndex Number indicating the index of the button to focus. 
*/
focus: function(p_nIndex) {

    var oButton;

    if(Lang.isNumber(p_nIndex)) {

        oButton = this._buttons[p_nIndex];
        
        if(oButton) {

            oButton.focus();

        }
    
    }
    else {

        var nButtons = this.getCount();

        for(var i=0; i<nButtons; i++) {

            oButton = this._buttons[i];

            if(!oButton.get("disabled")) {

                oButton.focus();
                break;

            }

        }

    }

},


/**
* @method check
* @description Checks the button at the specified index.
* @param {Number} p_nIndex Number indicating the index of the button to check. 
*/
check: function(p_nIndex) {

    var oButton = this.getButton(p_nIndex);
    
    if(oButton) {

        oButton.set("checked", true);
    
    }

},


/**
* @method destroy
* @description Removes the button group's element from its parent element and 
* removes all event handlers.
*/
destroy: function() {

    this.logger.log("Destroying...");

    var nButtons = this._buttons.length,
        oElement = this.get("element"),
        oParentNode = oElement.parentNode;
    
    if(nButtons > 0) {

        var i = this._buttons.length - 1;

        do {

            this._buttons[i].destroy();

        }
        while(i--);
    
    }

    this.logger.log("Removing DOM event handlers.");

    Event.purgeElement(oElement);
    
    this.logger.log("Removing from document.");

    oParentNode.removeChild(oElement);

},


/**
* @method toString
* @description Returns a string representing the button group.
* @return {String}
*/
toString: function() {

    return ("ButtonGroup " + this.get("id"));

}

});

})();
YAHOO.register("button", YAHOO.widget.Button, {version: "2.2.2", build: "204"});
