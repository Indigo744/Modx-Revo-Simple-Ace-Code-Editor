/**
* MODX highlight rules for ACE
* 
* Based on the work of danyaPostfactum, see link below
* https://github.com/danyaPostfactum/modx-ace/blob/master/assets/components/ace/modx.texteditor.js
* 
*/

modxCustomHighlightRules = function() {
    
    this.$rules['modxtag-comment'] = [
        {
            token : "comment.modx",
            regex : "[^\\[\\]]+",
            merge : true
        },{
            token : "comment.modx",
            regex : "\\[\\[\\-.*?\\]\\]"
        },{
            token : "comment.modx",
            regex : "\\s+",
            merge : true
        },
        {
            token : "paren.rparen.comment.modx",
            regex : "\\]\\]",
            next: "pop"
        }
    ];
    this.$rules['modxtag-start'] = [
        {
            token : ["cache-flag.variable.modx", "tag-token.variable.modx", "tag-name.variable.modx"],
            regex : "(!)?([%|*|~|\\+|\\$]|(?:\\+\\+)|(?:\\*#))?([-_a-zA-Z0-9\\.]+)",
            push : [
                {include: "modxtag-filter"},
                {
                    token: "tag-delimiter.keyword.operator.modx",
                    regex: "\\?",
                    push: [
                        {token : "text.modx", regex : "\\s+"},
                        {include: 'modxtag-property-string'},
                        {token: "", regex: "$"},
                        {token: '', regex: '', next: 'pop'}
                    ]
                },
                {token : "text.modx", regex : "\\s+"},
                {token: "", regex: "$"},
                {token: '', regex: '', next: 'pop'}
            ]
        },
        {
            token : "support.constant.paren.lparen.modx", // opening tag
            regex : "\\[\\[",
            push : 'modxtag-start'
        },
        {
            token : "text",
            regex : "\\s+"
        },
        {
            token : "support.constant.paren.rparen.tag-brackets.modx",
            regex : "\\]\\]",
            next: "pop"
        },
        {defaultToken: 'text.modx'}
    ];
    this.$rules['modxtag-propertyset'] = [
        {
            token : ['keyword.operator.modx', "support.class.modx"],
            regex : "(@)([-_a-zA-Z0-9\\.]+|\\[\\[.*?\\]\\])",
            next : 'modxtag-filter'
        },
        {
            token : "text",
            regex : "\\s+"
        },
        {token: "", regex: "$"},
        {
            token: "empty",
            regex: "",
            next: "modxtag-filter"
        }
    ];
    this.$rules['modxtag-filter'] = [
        {
            token : 'filter-delimiter.keyword.operator.modx',
            regex : ":",
            push : [
                {
                    token: "filter-name.support.function.modx",
                    regex: "[-_a-zA-Z0-9]+|\\[\\[.*?\\]\\]",
                    push: "modxtag-filter-eq"
                },
                {
                    token: "empty",
                    regex: "",
                    next: "pop"
                }
            ]
        },
        {
            token : "text",
            regex : "\\s+"
        }
    ];
    this.$rules['modxtag-filter-eq'] = [
        {
            token : ["keyword.operator.modx"],
            regex : "="
            },{
            token : 'string',
            regex : '`',
            push: "modxtag-filter-value"
        },
        {
            token : "text",
            regex : "\\s+"
        },
        {
            token: "empty",
            regex: "",
            next: "pop"
        }
    ];
    this.$rules["modxtag-property-string"] = [
        {
            token : "entity.other.attribute-name.modx",
            regex: "&"
        },
        {
            token: "entity.other.attribute-name.modx",
            regex: "[-_a-zA-Z0-9]+"
        },
        {
            token : "string.modx",
            regex : '`',
            push : "modxtag-attribute-value"
            }, {
            token : "keyword.operator.modx",
            regex : "="
            }, {
            token : "entity.other.attribute-name.modx",
            regex : "[-_a-zA-Z0-9]+"
        },
        {
            token : "comment.modx",
            regex : "\\[\\[\\-.*?\\]\\]"
        },
        {
            token : "property-string.text.modx",
            regex : "\\s+"
        }
    ];
    this.$rules["modxtag-attribute-value"] = [
        {
            token : "string.modx",
            regex : "[^`\\[]+",
            merge : true
            },{
            token : "string.modx",
            regex : "[^`]+",
            merge : true
            },/* {
            token : "string",
            regex : "\\\\$",
            next  : "modxtag-attribute-value",
            merge : true
            },*/ {
            token : "string.modx",
            regex : "`",
            next  : "pop",
            merge : true
        }
    ];
    this.$rules["modxtag-filter-value"] = [
        {
            token : "string.modx",
            regex : "[^`\\[]+",
            merge : true
            },{
            token : "string.modx",
            regex : "\\[\\[.*?\\]\\]",
            merge : true
            }, {
            token : "string.modx",
            regex : "\\\\$",
            next  : "pop",
            merge : true
            }, {
            token : "string.modx",
            regex : "`",
            next  : "pop",
            merge : true
        }
    ];

    // add twig start tags to the HTML start tags
    for (var rule in this.$rules) {
        this.$rules[rule].unshift({
            token : "paren.lparen.comment.modx", // opening tag
            regex : "\\[\\[\\-",
            push : 'modxtag-comment',
            merge: true
        }, {
            token : "support.constant.paren.lparen.tag-brackets.modx", // opening tag
            regex : "\\[\\[",
            push : 'modxtag-start',
            merge : false
        });
    }
};
