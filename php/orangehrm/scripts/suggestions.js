/**
 * Provides suggestions for state names (USA).
 * @class
 * @scope public
 */
function StateSuggestions(states) {
    this.states = states;
}

/**
 * Request suggestions for the given autosuggest control. 
 * @scope protected
 * @param oAutoSuggestControl The autosuggest control to provide suggestions for.
 */
StateSuggestions.prototype.requestSuggestions = function (oAutoSuggestControl /*:AutoSuggestControl*/,
                                                          bTypeAhead /*:boolean*/) {
    var aSuggestions = [];
    var sTextboxValue = oAutoSuggestControl.textbox.value;
    
    if (sTextboxValue.length > 0){
    
        //convert value in textbox to lowercase
        var sTextboxValueLC = sTextboxValue.toLowerCase();

        //search for matching states
        for (var i=0; i < this.states.length; i++) { 

            //convert state name to lowercase
            var sStateLC = this.states[i].toLowerCase();
           
            //compare the lowercase versions for case-insensitive comparison
            if (sStateLC.indexOf(sTextboxValueLC) == 0) {

                //add a suggestion using what's already in the textbox to begin it                
                aSuggestions.push(sTextboxValue + this.states[i].substring(sTextboxValue.length));
            } 
        }
    }

    //provide suggestions to the control
    oAutoSuggestControl.autosuggest(aSuggestions, bTypeAhead);
};
