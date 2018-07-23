

// Data & function(s) for ISO 3166-2 US State names and codes
// ISO 3166-2 US State names and codes
states = [
    {code: "AL", name: "Alabama"},
    {code: "AK", name: "Alaska"},
    {code: "AZ", name: "Arizona"},
    {code: "AR", name: "Arkansas"},
    {code: "CA", name: "California"},
    {code: "CO", name: "Colorado"},
    {code: "CT", name: "Connecticut"},
    {code: "DE", name: "Delaware"},
    {code: "FL", name: "Florida"},
    {code: "GA", name: "Georgia"},
    {code: "HI", name: "Hawaii"},
    {code: "ID", name: "Idaho"},
    {code: "IL", name: "Illinois"},
    {code: "IN", name: "Indiana"},
    {code: "IA", name: "Iowa"},
    {code: "KS", name: "Kansas"},
    {code: "KY", name: "Kentucky"},
    {code: "LA", name: "Louisiana"},
    {code: "ME", name: "Maine"},
    {code: "MD", name: "Maryland"},
    {code: "MA", name: "Massachusetts"},
    {code: "MI", name: "Michigan"},
    {code: "MN", name: "Minnesota"},
    {code: "MS", name: "Mississippi"},
    {code: "MO", name: "Missouri"},
    {code: "MT", name: "Montana"},
    {code: "NE", name: "Nebraska"},
    {code: "NV", name: "Nevada"},
    {code: "NH", name: "New Hampshire"},
    {code: "NJ", name: "New Jersey"},
    {code: "NM", name: "New Mexico"},
    {code: "NY", name: "New York"},
    {code: "NC", name: "North Carolina"},
    {code: "ND", name: "North Dakota"},
    {code: "OH", name: "Ohio"},
    {code: "OK", name: "Oklahoma"},
    {code: "OR", name: "Oregon"},
    {code: "PA", name: "Pennsylvania"},
    {code: "RI", name: "Rhode Island"},
    {code: "SC", name: "South Carolina"},
    {code: "SD", name: "South Dakota"},
    {code: "TN", name: "Tennessee"},
    {code: "TX", name: "Texas"},
    {code: "UT", name: "Utah"},
    {code: "VT", name: "Vermont"},
    {code: "VA", name: "Virginia"},
    {code: "WA", name: "Washington"},
    {code: "WV", name: "West Virginia"},
    {code: "WI", name: "Wisconsin"},
    {code: "WY", name: "Wyoming"}
];


// Get HTML for a list of Select options of ISO 3166-2 US State names and codes.
// eg "<option value="AL">Alabama</option>
//     <option value="AK" SELECTED>Alaska</option>"
// Parameters: strSelectedValue is a value that will be marked as "SELECTED"
//             if it is found in the options list
function getUsStateOptionsListHtml(strSelectedValue) {
    var strOptionsList = '<option value="">Please select...</option>\n';
    for (var i = 0; i < states.length; i++) {
        strOptionsList += '<option value="' + states[i].code + '"'
        if (strSelectedValue == states[i].code) {
            strOptionsList += " SELECTED"
        }
        strOptionsList += ">" + states[i].name + "</option>\n";
    }
    return strOptionsList;
}
// Get the US State name for a given code.
function getUsStateName(strStateCode) {
    for (var i = 0; i < states.length; i++) {
        if (strStateCode == states[i].code) {
            return states[i].name;
        }
    }
    return "";
} 


