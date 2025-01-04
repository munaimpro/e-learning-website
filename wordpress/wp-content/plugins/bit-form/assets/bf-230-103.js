var Ir=Object.defineProperty,Pr=Object.defineProperties;var Fr=Object.getOwnPropertyDescriptors;var Xt=Object.getOwnPropertySymbols;var Nr=Object.prototype.hasOwnProperty,zr=Object.prototype.propertyIsEnumerable;var Yt=(k,f,n)=>f in k?Ir(k,f,{enumerable:!0,configurable:!0,writable:!0,value:n}):k[f]=n,Oe=(k,f)=>{for(var n in f||(f={}))Nr.call(f,n)&&Yt(k,n,f[n]);if(Xt)for(var n of Xt(f))zr.call(f,n)&&Yt(k,n,f[n]);return k},We=(k,f)=>Pr(k,Fr(f));import{b9 as re,b6 as kt,r as he,Y as Dr,ao as Br,dN as pn,a as hn,j as W,bc as jr,aV as Hr,q as qr,v as Ur,h as Wr,a9 as Vr,c as ft,a6 as ye,_ as mt,I as bt,dK as Gr,aM as Kr,c6 as Xr,c7 as Yr,aP as Jr,d as Zr}from"./main-601.js";import{a as rt}from"./bf-216-304.js";import{S as Qr,D as ei,B as ti}from"./bf-548-69.js";import{C as Jt}from"./bf-540-305.js";import{L as ni}from"./bf-558-136.js";import{S as ri,G as Zt}from"./bf-184-110.js";import{P as Qt}from"./bf-241-73.js";import"./bf-409-72.js";import"./bf-156-137.js";import"./bf-635-81.js";import"./bf-833-79.js";import"./bf-443-82.js";var ii={exports:{}};(function(k,f){ace.define("ace/mode/css_highlight_rules",["require","exports","module","ace/lib/oop","ace/lib/lang","ace/mode/text_highlight_rules"],function(n,l,T){var e=n("../lib/oop");n("../lib/lang");var a=n("./text_highlight_rules").TextHighlightRules,u=l.supportType="align-content|align-items|align-self|all|animation|animation-delay|animation-direction|animation-duration|animation-fill-mode|animation-iteration-count|animation-name|animation-play-state|animation-timing-function|backface-visibility|background|background-attachment|background-blend-mode|background-clip|background-color|background-image|background-origin|background-position|background-repeat|background-size|border|border-bottom|border-bottom-color|border-bottom-left-radius|border-bottom-right-radius|border-bottom-style|border-bottom-width|border-collapse|border-color|border-image|border-image-outset|border-image-repeat|border-image-slice|border-image-source|border-image-width|border-left|border-left-color|border-left-style|border-left-width|border-radius|border-right|border-right-color|border-right-style|border-right-width|border-spacing|border-style|border-top|border-top-color|border-top-left-radius|border-top-right-radius|border-top-style|border-top-width|border-width|bottom|box-shadow|box-sizing|caption-side|clear|clip|color|column-count|column-fill|column-gap|column-rule|column-rule-color|column-rule-style|column-rule-width|column-span|column-width|columns|content|counter-increment|counter-reset|cursor|direction|display|empty-cells|filter|flex|flex-basis|flex-direction|flex-flow|flex-grow|flex-shrink|flex-wrap|float|font|font-family|font-size|font-size-adjust|font-stretch|font-style|font-variant|font-weight|hanging-punctuation|height|justify-content|left|letter-spacing|line-height|list-style|list-style-image|list-style-position|list-style-type|margin|margin-bottom|margin-left|margin-right|margin-top|max-height|max-width|max-zoom|min-height|min-width|min-zoom|nav-down|nav-index|nav-left|nav-right|nav-up|opacity|order|outline|outline-color|outline-offset|outline-style|outline-width|overflow|overflow-x|overflow-y|padding|padding-bottom|padding-left|padding-right|padding-top|page-break-after|page-break-before|page-break-inside|perspective|perspective-origin|position|quotes|resize|right|tab-size|table-layout|text-align|text-align-last|text-decoration|text-decoration-color|text-decoration-line|text-decoration-style|text-indent|text-justify|text-overflow|text-shadow|text-transform|top|transform|transform-origin|transform-style|transition|transition-delay|transition-duration|transition-property|transition-timing-function|unicode-bidi|user-select|user-zoom|vertical-align|visibility|white-space|width|word-break|word-spacing|word-wrap|z-index",g=l.supportFunction="rgb|rgba|url|attr|counter|counters",p=l.supportConstant="absolute|after-edge|after|all-scroll|all|alphabetic|always|antialiased|armenian|auto|avoid-column|avoid-page|avoid|balance|baseline|before-edge|before|below|bidi-override|block-line-height|block|bold|bolder|border-box|both|bottom|box|break-all|break-word|capitalize|caps-height|caption|center|central|char|circle|cjk-ideographic|clone|close-quote|col-resize|collapse|column|consider-shifts|contain|content-box|cover|crosshair|cubic-bezier|dashed|decimal-leading-zero|decimal|default|disabled|disc|disregard-shifts|distribute-all-lines|distribute-letter|distribute-space|distribute|dotted|double|e-resize|ease-in|ease-in-out|ease-out|ease|ellipsis|end|exclude-ruby|flex-end|flex-start|fill|fixed|georgian|glyphs|grid-height|groove|hand|hanging|hebrew|help|hidden|hiragana-iroha|hiragana|horizontal|icon|ideograph-alpha|ideograph-numeric|ideograph-parenthesis|ideograph-space|ideographic|inactive|include-ruby|inherit|initial|inline-block|inline-box|inline-line-height|inline-table|inline|inset|inside|inter-ideograph|inter-word|invert|italic|justify|katakana-iroha|katakana|keep-all|last|left|lighter|line-edge|line-through|line|linear|list-item|local|loose|lower-alpha|lower-greek|lower-latin|lower-roman|lowercase|lr-tb|ltr|mathematical|max-height|max-size|medium|menu|message-box|middle|move|n-resize|ne-resize|newspaper|no-change|no-close-quote|no-drop|no-open-quote|no-repeat|none|normal|not-allowed|nowrap|nw-resize|oblique|open-quote|outset|outside|overline|padding-box|page|pointer|pre-line|pre-wrap|pre|preserve-3d|progress|relative|repeat-x|repeat-y|repeat|replaced|reset-size|ridge|right|round|row-resize|rtl|s-resize|scroll|se-resize|separate|slice|small-caps|small-caption|solid|space|square|start|static|status-bar|step-end|step-start|steps|stretch|strict|sub|super|sw-resize|table-caption|table-cell|table-column-group|table-column|table-footer-group|table-header-group|table-row-group|table-row|table|tb-rl|text-after-edge|text-before-edge|text-bottom|text-size|text-top|text|thick|thin|transparent|underline|upper-alpha|upper-latin|upper-roman|uppercase|use-script|vertical-ideographic|vertical-text|visible|w-resize|wait|whitespace|z-index|zero|zoom",s=l.supportConstantColor="aliceblue|antiquewhite|aqua|aquamarine|azure|beige|bisque|black|blanchedalmond|blue|blueviolet|brown|burlywood|cadetblue|chartreuse|chocolate|coral|cornflowerblue|cornsilk|crimson|cyan|darkblue|darkcyan|darkgoldenrod|darkgray|darkgreen|darkgrey|darkkhaki|darkmagenta|darkolivegreen|darkorange|darkorchid|darkred|darksalmon|darkseagreen|darkslateblue|darkslategray|darkslategrey|darkturquoise|darkviolet|deeppink|deepskyblue|dimgray|dimgrey|dodgerblue|firebrick|floralwhite|forestgreen|fuchsia|gainsboro|ghostwhite|gold|goldenrod|gray|green|greenyellow|grey|honeydew|hotpink|indianred|indigo|ivory|khaki|lavender|lavenderblush|lawngreen|lemonchiffon|lightblue|lightcoral|lightcyan|lightgoldenrodyellow|lightgray|lightgreen|lightgrey|lightpink|lightsalmon|lightseagreen|lightskyblue|lightslategray|lightslategrey|lightsteelblue|lightyellow|lime|limegreen|linen|magenta|maroon|mediumaquamarine|mediumblue|mediumorchid|mediumpurple|mediumseagreen|mediumslateblue|mediumspringgreen|mediumturquoise|mediumvioletred|midnightblue|mintcream|mistyrose|moccasin|navajowhite|navy|oldlace|olive|olivedrab|orange|orangered|orchid|palegoldenrod|palegreen|paleturquoise|palevioletred|papayawhip|peachpuff|peru|pink|plum|powderblue|purple|rebeccapurple|red|rosybrown|royalblue|saddlebrown|salmon|sandybrown|seagreen|seashell|sienna|silver|skyblue|slateblue|slategray|slategrey|snow|springgreen|steelblue|tan|teal|thistle|tomato|turquoise|violet|wheat|white|whitesmoke|yellow|yellowgreen",o=l.supportConstantFonts="arial|century|comic|courier|cursive|fantasy|garamond|georgia|helvetica|impact|lucida|symbol|system|tahoma|times|trebuchet|utopia|verdana|webdings|sans-serif|serif|monospace",d=l.numRe="\\-?(?:(?:[0-9]+(?:\\.[0-9]+)?)|(?:\\.[0-9]+))",m=l.pseudoElements="(\\:+)\\b(after|before|first-letter|first-line|moz-selection|selection)\\b",_=l.pseudoClasses="(:)\\b(active|checked|disabled|empty|enabled|first-child|first-of-type|focus|hover|indeterminate|invalid|last-child|last-of-type|link|not|nth-child|nth-last-child|nth-last-of-type|nth-of-type|only-child|only-of-type|required|root|target|valid|visited)\\b",C=function(){var b=this.createKeywordMapper({"support.function":g,"support.constant":p,"support.type":u,"support.constant.color":s,"support.constant.fonts":o},"text",!0);this.$rules={start:[{include:["strings","url","comments"]},{token:"paren.lparen",regex:"\\{",next:"ruleset"},{token:"paren.rparen",regex:"\\}"},{token:"string",regex:"@(?!viewport)",next:"media"},{token:"keyword",regex:"#[a-z0-9-_]+"},{token:"keyword",regex:"%"},{token:"variable",regex:"\\.[a-z0-9-_]+"},{token:"string",regex:":[a-z0-9-_]+"},{token:"constant.numeric",regex:d},{token:"constant",regex:"[a-z0-9-_]+"},{caseInsensitive:!0}],media:[{include:["strings","url","comments"]},{token:"paren.lparen",regex:"\\{",next:"start"},{token:"paren.rparen",regex:"\\}",next:"start"},{token:"string",regex:";",next:"start"},{token:"keyword",regex:"(?:media|supports|document|charset|import|namespace|media|supports|document|page|font|keyframes|viewport|counter-style|font-feature-values|swash|ornaments|annotation|stylistic|styleset|character-variant)"}],comments:[{token:"comment",regex:"\\/\\*",push:[{token:"comment",regex:"\\*\\/",next:"pop"},{defaultToken:"comment"}]}],ruleset:[{regex:"-(webkit|ms|moz|o)-",token:"text"},{token:"punctuation.operator",regex:"[:;]"},{token:"paren.rparen",regex:"\\}",next:"start"},{include:["strings","url","comments"]},{token:["constant.numeric","keyword"],regex:"("+d+")(ch|cm|deg|em|ex|fr|gd|grad|Hz|in|kHz|mm|ms|pc|pt|px|rad|rem|s|turn|vh|vmax|vmin|vm|vw|%)"},{token:"constant.numeric",regex:d},{token:"constant.numeric",regex:"#[a-f0-9]{6}"},{token:"constant.numeric",regex:"#[a-f0-9]{3}"},{token:["punctuation","entity.other.attribute-name.pseudo-element.css"],regex:m},{token:["punctuation","entity.other.attribute-name.pseudo-class.css"],regex:_},{include:"url"},{token:b,regex:"\\-?[a-zA-Z_][a-zA-Z0-9_\\-]*"},{token:"paren.lparen",regex:"\\{"},{caseInsensitive:!0}],url:[{token:"support.function",regex:"(?:url(:?-prefix)?|domain|regexp)\\(",push:[{token:"support.function",regex:"\\)",next:"pop"},{defaultToken:"string"}]}],strings:[{token:"string.start",regex:"'",push:[{token:"string.end",regex:"'|$",next:"pop"},{include:"escapes"},{token:"constant.language.escape",regex:/\\$/,consumeLineEnd:!0},{defaultToken:"string"}]},{token:"string.start",regex:'"',push:[{token:"string.end",regex:'"|$',next:"pop"},{include:"escapes"},{token:"constant.language.escape",regex:/\\$/,consumeLineEnd:!0},{defaultToken:"string"}]}],escapes:[{token:"constant.language.escape",regex:/\\([a-fA-F\d]{1,6}|[^a-fA-F\d])/}]},this.normalizeRules()};e.inherits(C,a),l.CssHighlightRules=C}),ace.define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(n,l,T){var e=n("../range").Range,a=function(){};(function(){this.checkOutdent=function(u,g){return/^\s+$/.test(u)?/^\s*\}/.test(g):!1},this.autoOutdent=function(u,g){var p=u.getLine(g),s=p.match(/^(\s*\})/);if(!s)return 0;var o=s[1].length,d=u.findMatchingBracket({row:g,column:o});if(!d||d.row==g)return 0;var m=this.$getIndent(u.getLine(d.row));u.replace(new e(g,0,g,o-1),m)},this.$getIndent=function(u){return u.match(/^\s*/)[0]}}).call(a.prototype),l.MatchingBraceOutdent=a}),ace.define("ace/mode/css_completions",["require","exports","module"],function(n,l,T){var e={background:{"#$0":1},"background-color":{"#$0":1,transparent:1,fixed:1},"background-image":{"url('/$0')":1},"background-repeat":{repeat:1,"repeat-x":1,"repeat-y":1,"no-repeat":1,inherit:1},"background-position":{bottom:2,center:2,left:2,right:2,top:2,inherit:2},"background-attachment":{scroll:1,fixed:1},"background-size":{cover:1,contain:1},"background-clip":{"border-box":1,"padding-box":1,"content-box":1},"background-origin":{"border-box":1,"padding-box":1,"content-box":1},border:{"solid $0":1,"dashed $0":1,"dotted $0":1,"#$0":1},"border-color":{"#$0":1},"border-style":{solid:2,dashed:2,dotted:2,double:2,groove:2,hidden:2,inherit:2,inset:2,none:2,outset:2,ridged:2},"border-collapse":{collapse:1,separate:1},bottom:{px:1,em:1,"%":1},clear:{left:1,right:1,both:1,none:1},color:{"#$0":1,"rgb(#$00,0,0)":1},cursor:{default:1,pointer:1,move:1,text:1,wait:1,help:1,progress:1,"n-resize":1,"ne-resize":1,"e-resize":1,"se-resize":1,"s-resize":1,"sw-resize":1,"w-resize":1,"nw-resize":1},display:{none:1,block:1,inline:1,"inline-block":1,"table-cell":1},"empty-cells":{show:1,hide:1},float:{left:1,right:1,none:1},"font-family":{Arial:2,"Comic Sans MS":2,Consolas:2,"Courier New":2,Courier:2,Georgia:2,Monospace:2,"Sans-Serif":2,"Segoe UI":2,Tahoma:2,"Times New Roman":2,"Trebuchet MS":2,Verdana:1},"font-size":{px:1,em:1,"%":1},"font-weight":{bold:1,normal:1},"font-style":{italic:1,normal:1},"font-variant":{normal:1,"small-caps":1},height:{px:1,em:1,"%":1},left:{px:1,em:1,"%":1},"letter-spacing":{normal:1},"line-height":{normal:1},"list-style-type":{none:1,disc:1,circle:1,square:1,decimal:1,"decimal-leading-zero":1,"lower-roman":1,"upper-roman":1,"lower-greek":1,"lower-latin":1,"upper-latin":1,georgian:1,"lower-alpha":1,"upper-alpha":1},margin:{px:1,em:1,"%":1},"margin-right":{px:1,em:1,"%":1},"margin-left":{px:1,em:1,"%":1},"margin-top":{px:1,em:1,"%":1},"margin-bottom":{px:1,em:1,"%":1},"max-height":{px:1,em:1,"%":1},"max-width":{px:1,em:1,"%":1},"min-height":{px:1,em:1,"%":1},"min-width":{px:1,em:1,"%":1},overflow:{hidden:1,visible:1,auto:1,scroll:1},"overflow-x":{hidden:1,visible:1,auto:1,scroll:1},"overflow-y":{hidden:1,visible:1,auto:1,scroll:1},padding:{px:1,em:1,"%":1},"padding-top":{px:1,em:1,"%":1},"padding-right":{px:1,em:1,"%":1},"padding-bottom":{px:1,em:1,"%":1},"padding-left":{px:1,em:1,"%":1},"page-break-after":{auto:1,always:1,avoid:1,left:1,right:1},"page-break-before":{auto:1,always:1,avoid:1,left:1,right:1},position:{absolute:1,relative:1,fixed:1,static:1},right:{px:1,em:1,"%":1},"table-layout":{fixed:1,auto:1},"text-decoration":{none:1,underline:1,"line-through":1,blink:1},"text-align":{left:1,right:1,center:1,justify:1},"text-transform":{capitalize:1,uppercase:1,lowercase:1,none:1},top:{px:1,em:1,"%":1},"vertical-align":{top:1,bottom:1},visibility:{hidden:1,visible:1},"white-space":{nowrap:1,normal:1,pre:1,"pre-line":1,"pre-wrap":1},width:{px:1,em:1,"%":1},"word-spacing":{normal:1},filter:{"alpha(opacity=$0100)":1},"text-shadow":{"$02px 2px 2px #777":1},"text-overflow":{"ellipsis-word":1,clip:1,ellipsis:1},"-moz-border-radius":1,"-moz-border-radius-topright":1,"-moz-border-radius-bottomright":1,"-moz-border-radius-topleft":1,"-moz-border-radius-bottomleft":1,"-webkit-border-radius":1,"-webkit-border-top-right-radius":1,"-webkit-border-top-left-radius":1,"-webkit-border-bottom-right-radius":1,"-webkit-border-bottom-left-radius":1,"-moz-box-shadow":1,"-webkit-box-shadow":1,transform:{"rotate($00deg)":1,"skew($00deg)":1},"-moz-transform":{"rotate($00deg)":1,"skew($00deg)":1},"-webkit-transform":{"rotate($00deg)":1,"skew($00deg)":1}},a=function(){};(function(){this.completionsDefined=!1,this.defineCompletions=function(){if(document){var u=document.createElement("c").style;for(var g in u)if(typeof u[g]=="string"){var p=g.replace(/[A-Z]/g,function(s){return"-"+s.toLowerCase()});e.hasOwnProperty(p)||(e[p]=1)}}this.completionsDefined=!0},this.getCompletions=function(u,g,p,s){if(this.completionsDefined||this.defineCompletions(),u==="ruleset"||g.$mode.$id=="ace/mode/scss"){var o=g.getLine(p.row).substr(0,p.column),d=/\([^)]*$/.test(o);return d&&(o=o.substr(o.lastIndexOf("(")+1)),/:[^;]+$/.test(o)?this.getPropertyValueCompletions(u,g,p,s):this.getPropertyCompletions(u,g,p,s,d)}return[]},this.getPropertyCompletions=function(u,g,p,s,o){o=o||!1;var d=Object.keys(e);return d.map(function(m){return{caption:m,snippet:m+": $0"+(o?"":";"),meta:"property",score:1e6}})},this.getPropertyValueCompletions=function(u,g,p,s){var o=g.getLine(p.row).substr(0,p.column),d=(/([\w\-]+):[^:]*$/.exec(o)||{})[1];if(!d)return[];var m=[];return d in e&&typeof e[d]=="object"&&(m=Object.keys(e[d])),m.map(function(_){return{caption:_,snippet:_,meta:"property value",score:1e6}})}}).call(a.prototype),l.CssCompletions=a}),ace.define("ace/mode/behaviour/css",["require","exports","module","ace/lib/oop","ace/mode/behaviour","ace/mode/behaviour/cstyle","ace/token_iterator"],function(n,l,T){var e=n("../../lib/oop");n("../behaviour").Behaviour;var a=n("./cstyle").CstyleBehaviour,u=n("../../token_iterator").TokenIterator,g=function(){this.inherit(a),this.add("colon","insertion",function(p,s,o,d,m){if(m===":"&&o.selection.isEmpty()){var _=o.getCursorPosition(),C=new u(d,_.row,_.column),b=C.getCurrentToken();if(b&&b.value.match(/\s+/)&&(b=C.stepBackward()),b&&b.type==="support.type"){var y=d.doc.getLine(_.row),A=y.substring(_.column,_.column+1);if(A===":")return{text:"",selection:[1,1]};if(/^(\s+[^;]|\s*$)/.test(y.substring(_.column)))return{text:":;",selection:[1,1]}}}}),this.add("colon","deletion",function(p,s,o,d,m){var _=d.doc.getTextRange(m);if(!m.isMultiLine()&&_===":"){var C=o.getCursorPosition(),b=new u(d,C.row,C.column),y=b.getCurrentToken();if(y&&y.value.match(/\s+/)&&(y=b.stepBackward()),y&&y.type==="support.type"){var A=d.doc.getLine(m.start.row),I=A.substring(m.end.column,m.end.column+1);if(I===";")return m.end.column++,m}}}),this.add("semicolon","insertion",function(p,s,o,d,m){if(m===";"&&o.selection.isEmpty()){var _=o.getCursorPosition(),C=d.doc.getLine(_.row),b=C.substring(_.column,_.column+1);if(b===";")return{text:"",selection:[1,1]}}}),this.add("!important","insertion",function(p,s,o,d,m){if(m==="!"&&o.selection.isEmpty()){var _=o.getCursorPosition(),C=d.doc.getLine(_.row);if(/^\s*(;|}|$)/.test(C.substring(_.column)))return{text:"!important",selection:[10,10]}}})};e.inherits(g,a),l.CssBehaviour=g}),ace.define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(n,l,T){var e=n("../../lib/oop"),a=n("../../range").Range,u=n("./fold_mode").FoldMode,g=l.FoldMode=function(p){p&&(this.foldingStartMarker=new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/,"|"+p.start)),this.foldingStopMarker=new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/,"|"+p.end)))};e.inherits(g,u),function(){this.foldingStartMarker=/([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/,this.foldingStopMarker=/^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/,this.singleLineBlockCommentRe=/^\s*(\/\*).*\*\/\s*$/,this.tripleStarBlockCommentRe=/^\s*(\/\*\*\*).*\*\/\s*$/,this.startRegionRe=/^\s*(\/\*|\/\/)#?region\b/,this._getFoldWidgetBase=this.getFoldWidget,this.getFoldWidget=function(p,s,o){var d=p.getLine(o);if(this.singleLineBlockCommentRe.test(d)&&!this.startRegionRe.test(d)&&!this.tripleStarBlockCommentRe.test(d))return"";var m=this._getFoldWidgetBase(p,s,o);return!m&&this.startRegionRe.test(d)?"start":m},this.getFoldWidgetRange=function(p,s,o,d){var m=p.getLine(o);if(this.startRegionRe.test(m))return this.getCommentRegionBlock(p,m,o);var b=m.match(this.foldingStartMarker);if(b){var _=b.index;if(b[1])return this.openingBracketBlock(p,b[1],o,_);var C=p.getCommentFoldRange(o,_+b[0].length,1);return C&&!C.isMultiLine()&&(d?C=this.getSectionRange(p,o):s!="all"&&(C=null)),C}if(s!=="markbegin"){var b=m.match(this.foldingStopMarker);if(b){var _=b.index+b[0].length;return b[1]?this.closingBracketBlock(p,b[1],o,_):p.getCommentFoldRange(o,_,-1)}}},this.getSectionRange=function(p,s){var o=p.getLine(s),d=o.search(/\S/),m=s,_=o.length;s+=1;for(var C=s,b=p.getLength();++s<b;){o=p.getLine(s);var y=o.search(/\S/);if(y!==-1){if(d>y)break;var A=this.getFoldWidgetRange(p,"all",s);if(A){if(A.start.row<=m)break;if(A.isMultiLine())s=A.end.row;else if(d==y)break}C=s}}return new a(m,_,C,p.getLine(C).length)},this.getCommentRegionBlock=function(p,s,o){for(var d=s.search(/\s*$/),m=p.getLength(),_=o,C=/^\s*(?:\/\*|\/\/|--)#?(end)?region\b/,b=1;++o<m;){s=p.getLine(o);var y=C.exec(s);if(y&&(y[1]?b--:b++,!b))break}var A=o;if(A>_)return new a(_,d,A,s.length)}}.call(g.prototype)}),ace.define("ace/mode/css",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/css_highlight_rules","ace/mode/matching_brace_outdent","ace/worker/worker_client","ace/mode/css_completions","ace/mode/behaviour/css","ace/mode/folding/cstyle"],function(n,l,T){var e=n("../lib/oop"),a=n("./text").Mode,u=n("./css_highlight_rules").CssHighlightRules,g=n("./matching_brace_outdent").MatchingBraceOutdent,p=n("../worker/worker_client").WorkerClient,s=n("./css_completions").CssCompletions,o=n("./behaviour/css").CssBehaviour,d=n("./folding/cstyle").FoldMode,m=function(){this.HighlightRules=u,this.$outdent=new g,this.$behaviour=new o,this.$completer=new s,this.foldingRules=new d};e.inherits(m,a),function(){this.foldingRules="cStyle",this.blockComment={start:"/*",end:"*/"},this.getNextLineIndent=function(_,C,b){var y=this.$getIndent(C),A=this.getTokenizer().getLineTokens(C,_).tokens;if(A.length&&A[A.length-1].type=="comment")return y;var I=C.match(/^.*\{\s*$/);return I&&(y+=b),y},this.checkOutdent=function(_,C,b){return this.$outdent.checkOutdent(C,b)},this.autoOutdent=function(_,C,b){this.$outdent.autoOutdent(C,b)},this.getCompletions=function(_,C,b,y){return this.$completer.getCompletions(_,C,b,y)},this.createWorker=function(_){var C=new p(["ace"],"ace/mode/css_worker","Worker");return C.attachToDocument(_.getDocument()),C.on("annotate",function(b){_.setAnnotations(b.data)}),C.on("terminate",function(){_.clearAnnotations()}),C},this.$id="ace/mode/css",this.snippetFileId="ace/snippets/css"}.call(m.prototype),l.Mode=m}),function(){ace.require(["ace/mode/css"],function(n){k&&(k.exports=n)})}()})(ii);var oi={exports:{}};(function(k,f){ace.define("ace/mode/jsdoc_comment_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(n,l,T){var e=n("../lib/oop"),a=n("./text_highlight_rules").TextHighlightRules,u=function(){this.$rules={start:[{token:["comment.doc.tag","comment.doc.text","lparen.doc"],regex:"(@(?:param|member|typedef|property|namespace|var|const|callback))(\\s*)({)",push:[{token:"lparen.doc",regex:"{",push:[{include:"doc-syntax"},{token:"rparen.doc",regex:"}|(?=$)",next:"pop"}]},{token:["rparen.doc","text.doc","variable.parameter.doc","lparen.doc","variable.parameter.doc","rparen.doc"],regex:/(})(\s*)(?:([\w=:\/\.]+)|(?:(\[)([\w=:\/\.\-\'\" ]+)(\])))/,next:"pop"},{token:"rparen.doc",regex:"}|(?=$)",next:"pop"},{include:"doc-syntax"},{defaultToken:"text.doc"}]},{token:["comment.doc.tag","text.doc","lparen.doc"],regex:"(@(?:returns?|yields|type|this|suppress|public|protected|private|package|modifies|implements|external|exception|throws|enum|define|extends))(\\s*)({)",push:[{token:"lparen.doc",regex:"{",push:[{include:"doc-syntax"},{token:"rparen.doc",regex:"}|(?=$)",next:"pop"}]},{token:"rparen.doc",regex:"}|(?=$)",next:"pop"},{include:"doc-syntax"},{defaultToken:"text.doc"}]},{token:["comment.doc.tag","text.doc","variable.parameter.doc"],regex:'(@(?:alias|memberof|instance|module|name|lends|namespace|external|this|template|requires|param|implements|function|extends|typedef|mixes|constructor|var|memberof\\!|event|listens|exports|class|constructs|interface|emits|fires|throws|const|callback|borrows|augments))(\\s+)(\\w[\\w#.:/~"\\-]*)?'},{token:["comment.doc.tag","text.doc","variable.parameter.doc"],regex:"(@method)(\\s+)(\\w[\\w.\\(\\)]*)"},{token:"comment.doc.tag",regex:"@access\\s+(?:private|public|protected)"},{token:"comment.doc.tag",regex:"@kind\\s+(?:class|constant|event|external|file|function|member|mixin|module|namespace|typedef)"},{token:"comment.doc.tag",regex:"@\\w+(?=\\s|$)"},u.getTagRule(),{defaultToken:"comment.doc.body",caseInsensitive:!0}],"doc-syntax":[{token:"operator.doc",regex:/[|:]/},{token:"paren.doc",regex:/[\[\]]/}]},this.normalizeRules()};e.inherits(u,a),u.getTagRule=function(g){return{token:"comment.doc.tag.storage.type",regex:"\\b(?:TODO|FIXME|XXX|HACK)\\b"}},u.getStartRule=function(g){return{token:"comment.doc",regex:/\/\*\*(?!\/)/,next:g}},u.getEndRule=function(g){return{token:"comment.doc",regex:"\\*\\/",next:g}},l.JsDocCommentHighlightRules=u}),ace.define("ace/mode/javascript_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/jsdoc_comment_highlight_rules","ace/mode/text_highlight_rules"],function(n,l,T){function e(){var d=s.replace("\\d","\\d\\-"),m={onMatch:function(C,b,y){var A=C.charAt(1)=="/"?2:1;return A==1?(b!=this.nextState?y.unshift(this.next,this.nextState,0):y.unshift(this.next),y[2]++):A==2&&b==this.nextState&&(y[1]--,(!y[1]||y[1]<0)&&(y.shift(),y.shift())),[{type:"meta.tag.punctuation."+(A==1?"":"end-")+"tag-open.xml",value:C.slice(0,A)},{type:"meta.tag.tag-name.xml",value:C.substr(A)}]},regex:"</?(?:"+d+"|(?=>))",next:"jsxAttributes",nextState:"jsx"};this.$rules.start.unshift(m);var _={regex:"{",token:"paren.quasi.start",push:"start"};this.$rules.jsx=[_,m,{include:"reference"},{defaultToken:"string.xml"}],this.$rules.jsxAttributes=[{token:"meta.tag.punctuation.tag-close.xml",regex:"/?>",onMatch:function(C,b,y){return b==y[0]&&y.shift(),C.length==2&&(y[0]==this.nextState&&y[1]--,(!y[1]||y[1]<0)&&y.splice(0,2)),this.next=y[0]||"start",[{type:this.token,value:C}]},nextState:"jsx"},_,a("jsxAttributes"),{token:"entity.other.attribute-name.xml",regex:d},{token:"keyword.operator.attribute-equals.xml",regex:"="},{token:"text.tag-whitespace.xml",regex:"\\s+"},{token:"string.attribute-value.xml",regex:"'",stateName:"jsx_attr_q",push:[{token:"string.attribute-value.xml",regex:"'",next:"pop"},{include:"reference"},{defaultToken:"string.attribute-value.xml"}]},{token:"string.attribute-value.xml",regex:'"',stateName:"jsx_attr_qq",push:[{token:"string.attribute-value.xml",regex:'"',next:"pop"},{include:"reference"},{defaultToken:"string.attribute-value.xml"}]},m],this.$rules.reference=[{token:"constant.language.escape.reference.xml",regex:"(?:&#[0-9]+;)|(?:&#x[0-9a-fA-F]+;)|(?:&[a-zA-Z0-9_:\\.-]+;)"}]}function a(d){return[{token:"comment",regex:/\/\*/,next:[g.getTagRule(),{token:"comment",regex:"\\*\\/",next:d||"pop"},{defaultToken:"comment",caseInsensitive:!0}]},{token:"comment",regex:"\\/\\/",next:[g.getTagRule(),{token:"comment",regex:"$|^",next:d||"pop"},{defaultToken:"comment",caseInsensitive:!0}]}]}var u=n("../lib/oop"),g=n("./jsdoc_comment_highlight_rules").JsDocCommentHighlightRules,p=n("./text_highlight_rules").TextHighlightRules,s="[a-zA-Z\\$_¡-￿][a-zA-Z\\d\\$_¡-￿]*",o=function(d){var m={"variable.language":"Array|Boolean|Date|Function|Iterator|Number|Object|RegExp|String|Proxy|Symbol|Namespace|QName|XML|XMLList|ArrayBuffer|Float32Array|Float64Array|Int16Array|Int32Array|Int8Array|Uint16Array|Uint32Array|Uint8Array|Uint8ClampedArray|Error|EvalError|InternalError|RangeError|ReferenceError|StopIteration|SyntaxError|TypeError|URIError|decodeURI|decodeURIComponent|encodeURI|encodeURIComponent|eval|isFinite|isNaN|parseFloat|parseInt|JSON|Math|this|arguments|prototype|window|document",keyword:"const|yield|import|get|set|async|await|break|case|catch|continue|default|delete|do|else|finally|for|if|in|of|instanceof|new|return|switch|throw|try|typeof|let|var|while|with|debugger|__parent__|__count__|escape|unescape|with|__proto__|class|enum|extends|super|export|implements|private|public|interface|package|protected|static|constructor","storage.type":"const|let|var|function","constant.language":"null|Infinity|NaN|undefined","support.function":"alert","constant.language.boolean":"true|false"},_=this.createKeywordMapper(m,"identifier"),C="case|do|else|finally|in|instanceof|return|throw|try|typeof|yield|void",b="\\\\(?:x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|u{[0-9a-fA-F]{1,6}}|[0-2][0-7]{0,2}|3[0-7][0-7]?|[4-7][0-7]?|.)",y="(function)(\\s*)(\\*?)",A={token:["identifier","text","paren.lparen"],regex:"(\\b(?!"+Object.values(m).join("|")+"\\b)"+s+")(\\s*)(\\()"};this.$rules={no_regex:[g.getStartRule("doc-start"),a("no_regex"),A,{token:"string",regex:"'(?=.)",next:"qstring"},{token:"string",regex:'"(?=.)',next:"qqstring"},{token:"constant.numeric",regex:/0(?:[xX][0-9a-fA-F]+|[oO][0-7]+|[bB][01]+)\b/},{token:"constant.numeric",regex:/(?:\d\d*(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+\b)?/},{token:["entity.name.function","text","keyword.operator","text","storage.type","text","storage.type","text","paren.lparen"],regex:"("+s+")(\\s*)(=)(\\s*)"+y+"(\\s*)(\\()",next:"function_arguments"},{token:["storage.type","text","storage.type","text","text","entity.name.function","text","paren.lparen"],regex:"(function)(?:(?:(\\s*)(\\*)(\\s*))|(\\s+))("+s+")(\\s*)(\\()",next:"function_arguments"},{token:["entity.name.function","text","punctuation.operator","text","storage.type","text","storage.type","text","paren.lparen"],regex:"("+s+")(\\s*)(:)(\\s*)"+y+"(\\s*)(\\()",next:"function_arguments"},{token:["text","text","storage.type","text","storage.type","text","paren.lparen"],regex:"(:)(\\s*)"+y+"(\\s*)(\\()",next:"function_arguments"},{token:"keyword",regex:`from(?=\\s*('|"))`},{token:"keyword",regex:"(?:"+C+")\\b",next:"start"},{token:"support.constant",regex:/that\b/},{token:["storage.type","punctuation.operator","support.function.firebug"],regex:/(console)(\.)(warn|info|log|error|debug|time|trace|timeEnd|assert)\b/},{token:_,regex:s},{token:"punctuation.operator",regex:/[.](?![.])/,next:"property"},{token:"storage.type",regex:/=>/,next:"start"},{token:"keyword.operator",regex:/--|\+\+|\.{3}|===|==|=|!=|!==|<+=?|>+=?|!|&&|\|\||\?:|[!$%&*+\-~\/^]=?/,next:"start"},{token:"punctuation.operator",regex:/[?:,;.]/,next:"start"},{token:"paren.lparen",regex:/[\[({]/,next:"start"},{token:"paren.rparen",regex:/[\])}]/},{token:"comment",regex:/^#!.*$/}],property:[{token:"text",regex:"\\s+"},{token:"keyword.operator",regex:/=/},{token:["storage.type","text","storage.type","text","paren.lparen"],regex:y+"(\\s*)(\\()",next:"function_arguments"},{token:["storage.type","text","storage.type","text","text","entity.name.function","text","paren.lparen"],regex:"(function)(?:(?:(\\s*)(\\*)(\\s*))|(\\s+))(\\w+)(\\s*)(\\()",next:"function_arguments"},{token:"punctuation.operator",regex:/[.](?![.])/},{token:"support.function",regex:"prototype"},{token:"support.function",regex:/(s(?:h(?:ift|ow(?:Mod(?:elessDialog|alDialog)|Help))|croll(?:X|By(?:Pages|Lines)?|Y|To)?|t(?:op|rike)|i(?:n|zeToContent|debar|gnText)|ort|u(?:p|b(?:str(?:ing)?)?)|pli(?:ce|t)|e(?:nd|t(?:Re(?:sizable|questHeader)|M(?:i(?:nutes|lliseconds)|onth)|Seconds|Ho(?:tKeys|urs)|Year|Cursor|Time(?:out)?|Interval|ZOptions|Date|UTC(?:M(?:i(?:nutes|lliseconds)|onth)|Seconds|Hours|Date|FullYear)|FullYear|Active)|arch)|qrt|lice|avePreferences|mall)|h(?:ome|andleEvent)|navigate|c(?:har(?:CodeAt|At)|o(?:s|n(?:cat|textual|firm)|mpile)|eil|lear(?:Timeout|Interval)?|a(?:ptureEvents|ll)|reate(?:StyleSheet|Popup|EventObject))|t(?:o(?:GMTString|S(?:tring|ource)|U(?:TCString|pperCase)|Lo(?:caleString|werCase))|est|a(?:n|int(?:Enabled)?))|i(?:s(?:NaN|Finite)|ndexOf|talics)|d(?:isableExternalCapture|ump|etachEvent)|u(?:n(?:shift|taint|escape|watch)|pdateCommands)|j(?:oin|avaEnabled)|p(?:o(?:p|w)|ush|lugins.refresh|a(?:ddings|rse(?:Int|Float)?)|r(?:int|ompt|eference))|e(?:scape|nableExternalCapture|val|lementFromPoint|x(?:p|ec(?:Script|Command)?))|valueOf|UTC|queryCommand(?:State|Indeterm|Enabled|Value)|f(?:i(?:nd|lter|le(?:ModifiedDate|Size|CreatedDate|UpdatedDate)|xed)|o(?:nt(?:size|color)|rward|rEach)|loor|romCharCode)|watch|l(?:ink|o(?:ad|g)|astIndexOf)|a(?:sin|nchor|cos|t(?:tachEvent|ob|an(?:2)?)|pply|lert|b(?:s|ort))|r(?:ou(?:nd|teEvents)|e(?:size(?:By|To)|calc|turnValue|place|verse|l(?:oad|ease(?:Capture|Events)))|andom)|g(?:o|et(?:ResponseHeader|M(?:i(?:nutes|lliseconds)|onth)|Se(?:conds|lection)|Hours|Year|Time(?:zoneOffset)?|Da(?:y|te)|UTC(?:M(?:i(?:nutes|lliseconds)|onth)|Seconds|Hours|Da(?:y|te)|FullYear)|FullYear|A(?:ttention|llResponseHeaders)))|m(?:in|ove(?:B(?:y|elow)|To(?:Absolute)?|Above)|ergeAttributes|a(?:tch|rgins|x))|b(?:toa|ig|o(?:ld|rderWidths)|link|ack))\b(?=\()/},{token:"support.function.dom",regex:/(s(?:ub(?:stringData|mit)|plitText|e(?:t(?:NamedItem|Attribute(?:Node)?)|lect))|has(?:ChildNodes|Feature)|namedItem|c(?:l(?:ick|o(?:se|neNode))|reate(?:C(?:omment|DATASection|aption)|T(?:Head|extNode|Foot)|DocumentFragment|ProcessingInstruction|E(?:ntityReference|lement)|Attribute))|tabIndex|i(?:nsert(?:Row|Before|Cell|Data)|tem)|open|delete(?:Row|C(?:ell|aption)|T(?:Head|Foot)|Data)|focus|write(?:ln)?|a(?:dd|ppend(?:Child|Data))|re(?:set|place(?:Child|Data)|move(?:NamedItem|Child|Attribute(?:Node)?)?)|get(?:NamedItem|Element(?:sBy(?:Name|TagName|ClassName)|ById)|Attribute(?:Node)?)|blur)\b(?=\()/},{token:"support.constant",regex:/(s(?:ystemLanguage|cr(?:ipts|ollbars|een(?:X|Y|Top|Left))|t(?:yle(?:Sheets)?|atus(?:Text|bar)?)|ibling(?:Below|Above)|ource|uffixes|e(?:curity(?:Policy)?|l(?:ection|f)))|h(?:istory|ost(?:name)?|as(?:h|Focus))|y|X(?:MLDocument|SLDocument)|n(?:ext|ame(?:space(?:s|URI)|Prop))|M(?:IN_VALUE|AX_VALUE)|c(?:haracterSet|o(?:n(?:structor|trollers)|okieEnabled|lorDepth|mp(?:onents|lete))|urrent|puClass|l(?:i(?:p(?:boardData)?|entInformation)|osed|asses)|alle(?:e|r)|rypto)|t(?:o(?:olbar|p)|ext(?:Transform|Indent|Decoration|Align)|ags)|SQRT(?:1_2|2)|i(?:n(?:ner(?:Height|Width)|put)|ds|gnoreCase)|zIndex|o(?:scpu|n(?:readystatechange|Line)|uter(?:Height|Width)|p(?:sProfile|ener)|ffscreenBuffering)|NEGATIVE_INFINITY|d(?:i(?:splay|alog(?:Height|Top|Width|Left|Arguments)|rectories)|e(?:scription|fault(?:Status|Ch(?:ecked|arset)|View)))|u(?:ser(?:Profile|Language|Agent)|n(?:iqueID|defined)|pdateInterval)|_content|p(?:ixelDepth|ort|ersonalbar|kcs11|l(?:ugins|atform)|a(?:thname|dding(?:Right|Bottom|Top|Left)|rent(?:Window|Layer)?|ge(?:X(?:Offset)?|Y(?:Offset)?))|r(?:o(?:to(?:col|type)|duct(?:Sub)?|mpter)|e(?:vious|fix)))|e(?:n(?:coding|abledPlugin)|x(?:ternal|pando)|mbeds)|v(?:isibility|endor(?:Sub)?|Linkcolor)|URLUnencoded|P(?:I|OSITIVE_INFINITY)|f(?:ilename|o(?:nt(?:Size|Family|Weight)|rmName)|rame(?:s|Element)|gColor)|E|whiteSpace|l(?:i(?:stStyleType|n(?:eHeight|kColor))|o(?:ca(?:tion(?:bar)?|lName)|wsrc)|e(?:ngth|ft(?:Context)?)|a(?:st(?:M(?:odified|atch)|Index|Paren)|yer(?:s|X)|nguage))|a(?:pp(?:MinorVersion|Name|Co(?:deName|re)|Version)|vail(?:Height|Top|Width|Left)|ll|r(?:ity|guments)|Linkcolor|bove)|r(?:ight(?:Context)?|e(?:sponse(?:XML|Text)|adyState))|global|x|m(?:imeTypes|ultiline|enubar|argin(?:Right|Bottom|Top|Left))|L(?:N(?:10|2)|OG(?:10E|2E))|b(?:o(?:ttom|rder(?:Width|RightWidth|BottomWidth|Style|Color|TopWidth|LeftWidth))|ufferDepth|elow|ackground(?:Color|Image)))\b/},{token:"identifier",regex:s},{regex:"",token:"empty",next:"no_regex"}],start:[g.getStartRule("doc-start"),a("start"),{token:"string.regexp",regex:"\\/",next:"regex"},{token:"text",regex:"\\s+|^$",next:"start"},{token:"empty",regex:"",next:"no_regex"}],regex:[{token:"regexp.keyword.operator",regex:"\\\\(?:u[\\da-fA-F]{4}|x[\\da-fA-F]{2}|.)"},{token:"string.regexp",regex:"/[sxngimy]*",next:"no_regex"},{token:"invalid",regex:/\{\d+\b,?\d*\}[+*]|[+*$^?][+*]|[$^][?]|\?{3,}/},{token:"constant.language.escape",regex:/\(\?[:=!]|\)|\{\d+\b,?\d*\}|[+*]\?|[()$^+*?.]/},{token:"constant.language.delimiter",regex:/\|/},{token:"constant.language.escape",regex:/\[\^?/,next:"regex_character_class"},{token:"empty",regex:"$",next:"no_regex"},{defaultToken:"string.regexp"}],regex_character_class:[{token:"regexp.charclass.keyword.operator",regex:"\\\\(?:u[\\da-fA-F]{4}|x[\\da-fA-F]{2}|.)"},{token:"constant.language.escape",regex:"]",next:"regex"},{token:"constant.language.escape",regex:"-"},{token:"empty",regex:"$",next:"no_regex"},{defaultToken:"string.regexp.charachterclass"}],default_parameter:[{token:"string",regex:"'(?=.)",push:[{token:"string",regex:"'|$",next:"pop"},{include:"qstring"}]},{token:"string",regex:'"(?=.)',push:[{token:"string",regex:'"|$',next:"pop"},{include:"qqstring"}]},{token:"constant.language",regex:"null|Infinity|NaN|undefined"},{token:"constant.numeric",regex:/0(?:[xX][0-9a-fA-F]+|[oO][0-7]+|[bB][01]+)\b/},{token:"constant.numeric",regex:/(?:\d\d*(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+\b)?/},{token:"punctuation.operator",regex:",",next:"function_arguments"},{token:"text",regex:"\\s+"},{token:"punctuation.operator",regex:"$"},{token:"empty",regex:"",next:"no_regex"}],function_arguments:[a("function_arguments"),{token:"variable.parameter",regex:s},{token:"punctuation.operator",regex:","},{token:"text",regex:"\\s+"},{token:"punctuation.operator",regex:"$"},{token:"empty",regex:"",next:"no_regex"}],qqstring:[{token:"constant.language.escape",regex:b},{token:"string",regex:"\\\\$",consumeLineEnd:!0},{token:"string",regex:'"|$',next:"no_regex"},{defaultToken:"string"}],qstring:[{token:"constant.language.escape",regex:b},{token:"string",regex:"\\\\$",consumeLineEnd:!0},{token:"string",regex:"'|$",next:"no_regex"},{defaultToken:"string"}]},(!d||!d.noES6)&&(this.$rules.no_regex.unshift({regex:"[{}]",onMatch:function(I,R,S){if(this.next=I=="{"?this.nextState:"",I=="{"&&S.length)S.unshift("start",R);else if(I=="}"&&S.length&&(S.shift(),this.next=S.shift(),this.next.indexOf("string")!=-1||this.next.indexOf("jsx")!=-1))return"paren.quasi.end";return I=="{"?"paren.lparen":"paren.rparen"},nextState:"start"},{token:"string.quasi.start",regex:/`/,push:[{token:"constant.language.escape",regex:b},{token:"paren.quasi.start",regex:/\${/,push:"start"},{token:"string.quasi.end",regex:/`/,next:"pop"},{defaultToken:"string.quasi"}]},{token:["variable.parameter","text"],regex:"("+s+")(\\s*)(?=\\=>)"},{token:"paren.lparen",regex:"(\\()(?=[^\\(]+\\s*=>)",next:"function_arguments"},{token:"variable.language",regex:"(?:(?:(?:Weak)?(?:Set|Map))|Promise)\\b"}),this.$rules.function_arguments.unshift({token:"keyword.operator",regex:"=",next:"default_parameter"},{token:"keyword.operator",regex:"\\.{3}"}),this.$rules.property.unshift({token:"support.function",regex:"(findIndex|repeat|startsWith|endsWith|includes|isSafeInteger|trunc|cbrt|log2|log10|sign|then|catch|finally|resolve|reject|race|any|all|allSettled|keys|entries|isInteger)\\b(?=\\()"},{token:"constant.language",regex:"(?:MAX_SAFE_INTEGER|MIN_SAFE_INTEGER|EPSILON)\\b"}),(!d||d.jsx!=0)&&e.call(this)),this.embedRules(g,"doc-",[g.getEndRule("no_regex")]),this.normalizeRules()};u.inherits(o,p),l.JavaScriptHighlightRules=o}),ace.define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(n,l,T){var e=n("../range").Range,a=function(){};(function(){this.checkOutdent=function(u,g){return/^\s+$/.test(u)?/^\s*\}/.test(g):!1},this.autoOutdent=function(u,g){var p=u.getLine(g),s=p.match(/^(\s*\})/);if(!s)return 0;var o=s[1].length,d=u.findMatchingBracket({row:g,column:o});if(!d||d.row==g)return 0;var m=this.$getIndent(u.getLine(d.row));u.replace(new e(g,0,g,o-1),m)},this.$getIndent=function(u){return u.match(/^\s*/)[0]}}).call(a.prototype),l.MatchingBraceOutdent=a}),ace.define("ace/mode/behaviour/xml",["require","exports","module","ace/lib/oop","ace/mode/behaviour","ace/token_iterator"],function(n,l,T){function e(s,o){return s&&s.type.lastIndexOf(o+".xml")>-1}var a=n("../../lib/oop"),u=n("../behaviour").Behaviour,g=n("../../token_iterator").TokenIterator,p=function(){this.add("string_dquotes","insertion",function(s,o,d,m,_){if(_=='"'||_=="'"){var C=_,b=m.doc.getTextRange(d.getSelectionRange());if(b!==""&&b!=="'"&&b!='"'&&d.getWrapBehavioursEnabled())return{text:C+b+C,selection:!1};var y=d.getCursorPosition(),A=m.doc.getLine(y.row),I=A.substring(y.column,y.column+1),R=new g(m,y.row,y.column),S=R.getCurrentToken();if(I==C&&(e(S,"attribute-value")||e(S,"string")))return{text:"",selection:[1,1]};if(S||(S=R.stepBackward()),!S)return;for(;e(S,"tag-whitespace")||e(S,"whitespace");)S=R.stepBackward();var t=!I||I.match(/\s/);if(e(S,"attribute-equals")&&(t||I==">")||e(S,"decl-attribute-equals")&&(t||I=="?"))return{text:C+C,selection:[1,1]}}}),this.add("string_dquotes","deletion",function(s,o,d,m,_){var C=m.doc.getTextRange(_);if(!_.isMultiLine()&&(C=='"'||C=="'")){var b=m.doc.getLine(_.start.row),y=b.substring(_.start.column+1,_.start.column+2);if(y==C)return _.end.column++,_}}),this.add("autoclosing","insertion",function(s,o,d,m,_){if(_==">"){var C=d.getSelectionRange().start,b=new g(m,C.row,C.column),y=b.getCurrentToken()||b.stepBackward();if(!y||!(e(y,"tag-name")||e(y,"tag-whitespace")||e(y,"attribute-name")||e(y,"attribute-equals")||e(y,"attribute-value"))||e(y,"reference.attribute-value"))return;if(e(y,"attribute-value")){var A=b.getCurrentTokenColumn()+y.value.length;if(C.column<A)return;if(C.column==A){var I=b.stepForward();if(I&&e(I,"attribute-value"))return;b.stepBackward()}}if(/^\s*>/.test(m.getLine(C.row).slice(C.column)))return;for(;!e(y,"tag-name");)if(y=b.stepBackward(),y.value=="<"){y=b.stepForward();break}var R=b.getCurrentTokenRow(),S=b.getCurrentTokenColumn();if(e(b.stepBackward(),"end-tag-open"))return;var t=y.value;return R==C.row&&(t=t.substring(0,C.column-S)),this.voidElements&&this.voidElements.hasOwnProperty(t.toLowerCase())?void 0:{text:"></"+t+">",selection:[1,1]}}}),this.add("autoindent","insertion",function(s,o,d,m,_){if(_==`
`){var C=d.getCursorPosition(),b=m.getLine(C.row),y=new g(m,C.row,C.column),A=y.getCurrentToken();if(e(A,"")&&A.type.indexOf("tag-close")!==-1){if(A.value=="/>")return;for(;A&&A.type.indexOf("tag-name")===-1;)A=y.stepBackward();if(!A)return;var I=A.value,R=y.getCurrentTokenRow();if(A=y.stepBackward(),!A||A.type.indexOf("end-tag")!==-1)return;if(this.voidElements&&!this.voidElements[I]||!this.voidElements){var S=m.getTokenAt(C.row,C.column+1),b=m.getLine(R),t=this.$getIndent(b),i=t+m.getTabString();return S&&S.value==="</"?{text:`
`+i+`
`+t,selection:[1,i.length,1,i.length]}:{text:`
`+i}}}}})};a.inherits(p,u),l.XmlBehaviour=p}),ace.define("ace/mode/behaviour/javascript",["require","exports","module","ace/lib/oop","ace/token_iterator","ace/mode/behaviour/cstyle","ace/mode/behaviour/xml"],function(n,l,T){var e=n("../../lib/oop"),a=n("../../token_iterator").TokenIterator,u=n("../behaviour/cstyle").CstyleBehaviour,g=n("../behaviour/xml").XmlBehaviour,p=function(){var s=new g({closeCurlyBraces:!0}).getBehaviours();this.addBehaviours(s),this.inherit(u),this.add("autoclosing-fragment","insertion",function(o,d,m,_,C){if(C==">"){var b=m.getSelectionRange().start,y=new a(_,b.row,b.column),A=y.getCurrentToken()||y.stepBackward();if(!A)return;if(A.value=="<")return{text:"></>",selection:[1,1]}}})};e.inherits(p,u),l.JavaScriptBehaviour=p}),ace.define("ace/mode/folding/xml",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(n,l,T){function e(o,d){return o.type.lastIndexOf(d+".xml")>-1}var a=n("../../lib/oop"),u=n("../../range").Range,g=n("./fold_mode").FoldMode,p=l.FoldMode=function(o,d){g.call(this),this.voidElements=o||{},this.optionalEndTags=a.mixin({},this.voidElements),d&&a.mixin(this.optionalEndTags,d)};a.inherits(p,g);var s=function(){this.tagName="",this.closing=!1,this.selfClosing=!1,this.start={row:0,column:0},this.end={row:0,column:0}};(function(){this.getFoldWidget=function(o,d,m){var _=this._getFirstTagInLine(o,m);return _?_.closing||!_.tagName&&_.selfClosing?d==="markbeginend"?"end":"":!_.tagName||_.selfClosing||this.voidElements.hasOwnProperty(_.tagName.toLowerCase())||this._findEndTagInLine(o,m,_.tagName,_.end.column)?"":"start":this.getCommentFoldWidget(o,m)},this.getCommentFoldWidget=function(o,d){return/comment/.test(o.getState(d))&&/<!-/.test(o.getLine(d))?"start":""},this._getFirstTagInLine=function(o,d){for(var m=o.getTokens(d),_=new s,C=0;C<m.length;C++){var b=m[C];if(e(b,"tag-open")){if(_.end.column=_.start.column+b.value.length,_.closing=e(b,"end-tag-open"),b=m[++C],!b)return null;if(_.tagName=b.value,b.value===""){if(b=m[++C],!b)return null;_.tagName=b.value}for(_.end.column+=b.value.length,C++;C<m.length;C++)if(b=m[C],_.end.column+=b.value.length,e(b,"tag-close")){_.selfClosing=b.value=="/>";break}return _}if(e(b,"tag-close"))return _.selfClosing=b.value=="/>",_;_.start.column+=b.value.length}return null},this._findEndTagInLine=function(o,d,m,_){for(var C=o.getTokens(d),b=0,y=0;y<C.length;y++){var A=C[y];if(b+=A.value.length,!(b<_-1)&&e(A,"end-tag-open")&&(A=C[y+1],e(A,"tag-name")&&A.value===""&&(A=C[y+2]),A&&A.value==m))return!0}return!1},this.getFoldWidgetRange=function(o,d,m){var _=this._getFirstTagInLine(o,m);if(!_)return this.getCommentFoldWidget(o,m)&&o.getCommentFoldRange(m,o.getLine(m).length);var C=o.getMatchingTags({row:m,column:0});if(C)return new u(C.openTag.end.row,C.openTag.end.column,C.closeTag.start.row,C.closeTag.start.column)}}).call(p.prototype)}),ace.define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(n,l,T){var e=n("../../lib/oop"),a=n("../../range").Range,u=n("./fold_mode").FoldMode,g=l.FoldMode=function(p){p&&(this.foldingStartMarker=new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/,"|"+p.start)),this.foldingStopMarker=new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/,"|"+p.end)))};e.inherits(g,u),function(){this.foldingStartMarker=/([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/,this.foldingStopMarker=/^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/,this.singleLineBlockCommentRe=/^\s*(\/\*).*\*\/\s*$/,this.tripleStarBlockCommentRe=/^\s*(\/\*\*\*).*\*\/\s*$/,this.startRegionRe=/^\s*(\/\*|\/\/)#?region\b/,this._getFoldWidgetBase=this.getFoldWidget,this.getFoldWidget=function(p,s,o){var d=p.getLine(o);if(this.singleLineBlockCommentRe.test(d)&&!this.startRegionRe.test(d)&&!this.tripleStarBlockCommentRe.test(d))return"";var m=this._getFoldWidgetBase(p,s,o);return!m&&this.startRegionRe.test(d)?"start":m},this.getFoldWidgetRange=function(p,s,o,d){var m=p.getLine(o);if(this.startRegionRe.test(m))return this.getCommentRegionBlock(p,m,o);var b=m.match(this.foldingStartMarker);if(b){var _=b.index;if(b[1])return this.openingBracketBlock(p,b[1],o,_);var C=p.getCommentFoldRange(o,_+b[0].length,1);return C&&!C.isMultiLine()&&(d?C=this.getSectionRange(p,o):s!="all"&&(C=null)),C}if(s!=="markbegin"){var b=m.match(this.foldingStopMarker);if(b){var _=b.index+b[0].length;return b[1]?this.closingBracketBlock(p,b[1],o,_):p.getCommentFoldRange(o,_,-1)}}},this.getSectionRange=function(p,s){var o=p.getLine(s),d=o.search(/\S/),m=s,_=o.length;s+=1;for(var C=s,b=p.getLength();++s<b;){o=p.getLine(s);var y=o.search(/\S/);if(y!==-1){if(d>y)break;var A=this.getFoldWidgetRange(p,"all",s);if(A){if(A.start.row<=m)break;if(A.isMultiLine())s=A.end.row;else if(d==y)break}C=s}}return new a(m,_,C,p.getLine(C).length)},this.getCommentRegionBlock=function(p,s,o){for(var d=s.search(/\s*$/),m=p.getLength(),_=o,C=/^\s*(?:\/\*|\/\/|--)#?(end)?region\b/,b=1;++o<m;){s=p.getLine(o);var y=C.exec(s);if(y&&(y[1]?b--:b++,!b))break}var A=o;if(A>_)return new a(_,d,A,s.length)}}.call(g.prototype)}),ace.define("ace/mode/folding/javascript",["require","exports","module","ace/lib/oop","ace/mode/folding/xml","ace/mode/folding/cstyle"],function(n,l,T){var e=n("../../lib/oop"),a=n("./xml").FoldMode,u=n("./cstyle").FoldMode,g=l.FoldMode=function(p){p&&(this.foldingStartMarker=new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/,"|"+p.start)),this.foldingStopMarker=new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/,"|"+p.end))),this.xmlFoldMode=new a};e.inherits(g,u),function(){this.getFoldWidgetRangeBase=this.getFoldWidgetRange,this.getFoldWidgetBase=this.getFoldWidget,this.getFoldWidget=function(p,s,o){var d=this.getFoldWidgetBase(p,s,o);return d||this.xmlFoldMode.getFoldWidget(p,s,o)},this.getFoldWidgetRange=function(p,s,o,d){var m=this.getFoldWidgetRangeBase(p,s,o,d);return m||this.xmlFoldMode.getFoldWidgetRange(p,s,o)}}.call(g.prototype)}),ace.define("ace/mode/javascript",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/javascript_highlight_rules","ace/mode/matching_brace_outdent","ace/worker/worker_client","ace/mode/behaviour/javascript","ace/mode/folding/javascript"],function(n,l,T){var e=n("../lib/oop"),a=n("./text").Mode,u=n("./javascript_highlight_rules").JavaScriptHighlightRules,g=n("./matching_brace_outdent").MatchingBraceOutdent,p=n("../worker/worker_client").WorkerClient,s=n("./behaviour/javascript").JavaScriptBehaviour,o=n("./folding/javascript").FoldMode,d=function(){this.HighlightRules=u,this.$outdent=new g,this.$behaviour=new s,this.foldingRules=new o};e.inherits(d,a),function(){this.lineCommentStart="//",this.blockComment={start:"/*",end:"*/"},this.$quotes={'"':'"',"'":"'","`":"`"},this.$pairQuotesAfter={"`":/\w/},this.getNextLineIndent=function(m,_,C){var b=this.$getIndent(_),y=this.getTokenizer().getLineTokens(_,m),A=y.tokens,I=y.state;if(A.length&&A[A.length-1].type=="comment")return b;if(m=="start"||m=="no_regex"){var R=_.match(/^.*(?:\bcase\b.*:|[\{\(\[])\s*$/);R&&(b+=C)}else if(m=="doc-start"&&(I=="start"||I=="no_regex"))return"";return b},this.checkOutdent=function(m,_,C){return this.$outdent.checkOutdent(_,C)},this.autoOutdent=function(m,_,C){this.$outdent.autoOutdent(_,C)},this.createWorker=function(m){var _=new p(["ace"],"ace/mode/javascript_worker","JavaScriptWorker");return _.attachToDocument(m.getDocument()),_.on("annotate",function(C){m.setAnnotations(C.data)}),_.on("terminate",function(){m.clearAnnotations()}),_},this.$id="ace/mode/javascript",this.snippetFileId="ace/snippets/javascript"}.call(d.prototype),l.Mode=d}),function(){ace.require(["ace/mode/javascript"],function(n){k&&(k.exports=n)})}()})(oi);var si={exports:{}};(function(k,f){ace.define("ace/snippets/css.snippets",["require","exports","module"],function(n,l,T){T.exports=`snippet .
	\${1} {
		\${2}
	}
snippet !
	 !important
snippet bdi:m+
	-moz-border-image: url(\${1}) \${2:0} \${3:0} \${4:0} \${5:0} \${6:stretch} \${7:stretch};
snippet bdi:m
	-moz-border-image: \${1};
snippet bdrz:m
	-moz-border-radius: \${1};
snippet bxsh:m+
	-moz-box-shadow: \${1:0} \${2:0} \${3:0} #\${4:000};
snippet bxsh:m
	-moz-box-shadow: \${1};
snippet bdi:w+
	-webkit-border-image: url(\${1}) \${2:0} \${3:0} \${4:0} \${5:0} \${6:stretch} \${7:stretch};
snippet bdi:w
	-webkit-border-image: \${1};
snippet bdrz:w
	-webkit-border-radius: \${1};
snippet bxsh:w+
	-webkit-box-shadow: \${1:0} \${2:0} \${3:0} #\${4:000};
snippet bxsh:w
	-webkit-box-shadow: \${1};
snippet @f
	@font-face {
		font-family: \${1};
		src: url(\${2});
	}
snippet @i
	@import url(\${1});
snippet @m
	@media \${1:print} {
		\${2}
	}
snippet bg+
	background: #\${1:FFF} url(\${2}) \${3:0} \${4:0} \${5:no-repeat};
snippet bga
	background-attachment: \${1};
snippet bga:f
	background-attachment: fixed;
snippet bga:s
	background-attachment: scroll;
snippet bgbk
	background-break: \${1};
snippet bgbk:bb
	background-break: bounding-box;
snippet bgbk:c
	background-break: continuous;
snippet bgbk:eb
	background-break: each-box;
snippet bgcp
	background-clip: \${1};
snippet bgcp:bb
	background-clip: border-box;
snippet bgcp:cb
	background-clip: content-box;
snippet bgcp:nc
	background-clip: no-clip;
snippet bgcp:pb
	background-clip: padding-box;
snippet bgc
	background-color: #\${1:FFF};
snippet bgc:t
	background-color: transparent;
snippet bgi
	background-image: url(\${1});
snippet bgi:n
	background-image: none;
snippet bgo
	background-origin: \${1};
snippet bgo:bb
	background-origin: border-box;
snippet bgo:cb
	background-origin: content-box;
snippet bgo:pb
	background-origin: padding-box;
snippet bgpx
	background-position-x: \${1};
snippet bgpy
	background-position-y: \${1};
snippet bgp
	background-position: \${1:0} \${2:0};
snippet bgr
	background-repeat: \${1};
snippet bgr:n
	background-repeat: no-repeat;
snippet bgr:x
	background-repeat: repeat-x;
snippet bgr:y
	background-repeat: repeat-y;
snippet bgr:r
	background-repeat: repeat;
snippet bgz
	background-size: \${1};
snippet bgz:a
	background-size: auto;
snippet bgz:ct
	background-size: contain;
snippet bgz:cv
	background-size: cover;
snippet bg
	background: \${1};
snippet bg:ie
	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='\${1}',sizingMethod='\${2:crop}');
snippet bg:n
	background: none;
snippet bd+
	border: \${1:1px} \${2:solid} #\${3:000};
snippet bdb+
	border-bottom: \${1:1px} \${2:solid} #\${3:000};
snippet bdbc
	border-bottom-color: #\${1:000};
snippet bdbi
	border-bottom-image: url(\${1});
snippet bdbi:n
	border-bottom-image: none;
snippet bdbli
	border-bottom-left-image: url(\${1});
snippet bdbli:c
	border-bottom-left-image: continue;
snippet bdbli:n
	border-bottom-left-image: none;
snippet bdblrz
	border-bottom-left-radius: \${1};
snippet bdbri
	border-bottom-right-image: url(\${1});
snippet bdbri:c
	border-bottom-right-image: continue;
snippet bdbri:n
	border-bottom-right-image: none;
snippet bdbrrz
	border-bottom-right-radius: \${1};
snippet bdbs
	border-bottom-style: \${1};
snippet bdbs:n
	border-bottom-style: none;
snippet bdbw
	border-bottom-width: \${1};
snippet bdb
	border-bottom: \${1};
snippet bdb:n
	border-bottom: none;
snippet bdbk
	border-break: \${1};
snippet bdbk:c
	border-break: close;
snippet bdcl
	border-collapse: \${1};
snippet bdcl:c
	border-collapse: collapse;
snippet bdcl:s
	border-collapse: separate;
snippet bdc
	border-color: #\${1:000};
snippet bdci
	border-corner-image: url(\${1});
snippet bdci:c
	border-corner-image: continue;
snippet bdci:n
	border-corner-image: none;
snippet bdf
	border-fit: \${1};
snippet bdf:c
	border-fit: clip;
snippet bdf:of
	border-fit: overwrite;
snippet bdf:ow
	border-fit: overwrite;
snippet bdf:r
	border-fit: repeat;
snippet bdf:sc
	border-fit: scale;
snippet bdf:sp
	border-fit: space;
snippet bdf:st
	border-fit: stretch;
snippet bdi
	border-image: url(\${1}) \${2:0} \${3:0} \${4:0} \${5:0} \${6:stretch} \${7:stretch};
snippet bdi:n
	border-image: none;
snippet bdl+
	border-left: \${1:1px} \${2:solid} #\${3:000};
snippet bdlc
	border-left-color: #\${1:000};
snippet bdli
	border-left-image: url(\${1});
snippet bdli:n
	border-left-image: none;
snippet bdls
	border-left-style: \${1};
snippet bdls:n
	border-left-style: none;
snippet bdlw
	border-left-width: \${1};
snippet bdl
	border-left: \${1};
snippet bdl:n
	border-left: none;
snippet bdlt
	border-length: \${1};
snippet bdlt:a
	border-length: auto;
snippet bdrz
	border-radius: \${1};
snippet bdr+
	border-right: \${1:1px} \${2:solid} #\${3:000};
snippet bdrc
	border-right-color: #\${1:000};
snippet bdri
	border-right-image: url(\${1});
snippet bdri:n
	border-right-image: none;
snippet bdrs
	border-right-style: \${1};
snippet bdrs:n
	border-right-style: none;
snippet bdrw
	border-right-width: \${1};
snippet bdr
	border-right: \${1};
snippet bdr:n
	border-right: none;
snippet bdsp
	border-spacing: \${1};
snippet bds
	border-style: \${1};
snippet bds:ds
	border-style: dashed;
snippet bds:dtds
	border-style: dot-dash;
snippet bds:dtdtds
	border-style: dot-dot-dash;
snippet bds:dt
	border-style: dotted;
snippet bds:db
	border-style: double;
snippet bds:g
	border-style: groove;
snippet bds:h
	border-style: hidden;
snippet bds:i
	border-style: inset;
snippet bds:n
	border-style: none;
snippet bds:o
	border-style: outset;
snippet bds:r
	border-style: ridge;
snippet bds:s
	border-style: solid;
snippet bds:w
	border-style: wave;
snippet bdt+
	border-top: \${1:1px} \${2:solid} #\${3:000};
snippet bdtc
	border-top-color: #\${1:000};
snippet bdti
	border-top-image: url(\${1});
snippet bdti:n
	border-top-image: none;
snippet bdtli
	border-top-left-image: url(\${1});
snippet bdtli:c
	border-corner-image: continue;
snippet bdtli:n
	border-corner-image: none;
snippet bdtlrz
	border-top-left-radius: \${1};
snippet bdtri
	border-top-right-image: url(\${1});
snippet bdtri:c
	border-top-right-image: continue;
snippet bdtri:n
	border-top-right-image: none;
snippet bdtrrz
	border-top-right-radius: \${1};
snippet bdts
	border-top-style: \${1};
snippet bdts:n
	border-top-style: none;
snippet bdtw
	border-top-width: \${1};
snippet bdt
	border-top: \${1};
snippet bdt:n
	border-top: none;
snippet bdw
	border-width: \${1};
snippet bd
	border: \${1};
snippet bd:n
	border: none;
snippet b
	bottom: \${1};
snippet b:a
	bottom: auto;
snippet bxsh+
	box-shadow: \${1:0} \${2:0} \${3:0} #\${4:000};
snippet bxsh
	box-shadow: \${1};
snippet bxsh:n
	box-shadow: none;
snippet bxz
	box-sizing: \${1};
snippet bxz:bb
	box-sizing: border-box;
snippet bxz:cb
	box-sizing: content-box;
snippet cps
	caption-side: \${1};
snippet cps:b
	caption-side: bottom;
snippet cps:t
	caption-side: top;
snippet cl
	clear: \${1};
snippet cl:b
	clear: both;
snippet cl:l
	clear: left;
snippet cl:n
	clear: none;
snippet cl:r
	clear: right;
snippet cp
	clip: \${1};
snippet cp:a
	clip: auto;
snippet cp:r
	clip: rect(\${1:0} \${2:0} \${3:0} \${4:0});
snippet c
	color: #\${1:000};
snippet ct
	content: \${1};
snippet ct:a
	content: attr(\${1});
snippet ct:cq
	content: close-quote;
snippet ct:c
	content: counter(\${1});
snippet ct:cs
	content: counters(\${1});
snippet ct:ncq
	content: no-close-quote;
snippet ct:noq
	content: no-open-quote;
snippet ct:n
	content: normal;
snippet ct:oq
	content: open-quote;
snippet coi
	counter-increment: \${1};
snippet cor
	counter-reset: \${1};
snippet cur
	cursor: \${1};
snippet cur:a
	cursor: auto;
snippet cur:c
	cursor: crosshair;
snippet cur:d
	cursor: default;
snippet cur:ha
	cursor: hand;
snippet cur:he
	cursor: help;
snippet cur:m
	cursor: move;
snippet cur:p
	cursor: pointer;
snippet cur:t
	cursor: text;
snippet d
	display: \${1};
snippet d:mib
	display: -moz-inline-box;
snippet d:mis
	display: -moz-inline-stack;
snippet d:b
	display: block;
snippet d:cp
	display: compact;
snippet d:ib
	display: inline-block;
snippet d:itb
	display: inline-table;
snippet d:i
	display: inline;
snippet d:li
	display: list-item;
snippet d:n
	display: none;
snippet d:ri
	display: run-in;
snippet d:tbcp
	display: table-caption;
snippet d:tbc
	display: table-cell;
snippet d:tbclg
	display: table-column-group;
snippet d:tbcl
	display: table-column;
snippet d:tbfg
	display: table-footer-group;
snippet d:tbhg
	display: table-header-group;
snippet d:tbrg
	display: table-row-group;
snippet d:tbr
	display: table-row;
snippet d:tb
	display: table;
snippet ec
	empty-cells: \${1};
snippet ec:h
	empty-cells: hide;
snippet ec:s
	empty-cells: show;
snippet exp
	expression()
snippet fl
	float: \${1};
snippet fl:l
	float: left;
snippet fl:n
	float: none;
snippet fl:r
	float: right;
snippet f+
	font: \${1:1em} \${2:Arial},\${3:sans-serif};
snippet fef
	font-effect: \${1};
snippet fef:eb
	font-effect: emboss;
snippet fef:eg
	font-effect: engrave;
snippet fef:n
	font-effect: none;
snippet fef:o
	font-effect: outline;
snippet femp
	font-emphasize-position: \${1};
snippet femp:a
	font-emphasize-position: after;
snippet femp:b
	font-emphasize-position: before;
snippet fems
	font-emphasize-style: \${1};
snippet fems:ac
	font-emphasize-style: accent;
snippet fems:c
	font-emphasize-style: circle;
snippet fems:ds
	font-emphasize-style: disc;
snippet fems:dt
	font-emphasize-style: dot;
snippet fems:n
	font-emphasize-style: none;
snippet fem
	font-emphasize: \${1};
snippet ff
	font-family: \${1};
snippet ff:c
	font-family: \${1:'Monotype Corsiva','Comic Sans MS'},cursive;
snippet ff:f
	font-family: \${1:Capitals,Impact},fantasy;
snippet ff:m
	font-family: \${1:Monaco,'Courier New'},monospace;
snippet ff:ss
	font-family: \${1:Helvetica,Arial},sans-serif;
snippet ff:s
	font-family: \${1:Georgia,'Times New Roman'},serif;
snippet fza
	font-size-adjust: \${1};
snippet fza:n
	font-size-adjust: none;
snippet fz
	font-size: \${1};
snippet fsm
	font-smooth: \${1};
snippet fsm:aw
	font-smooth: always;
snippet fsm:a
	font-smooth: auto;
snippet fsm:n
	font-smooth: never;
snippet fst
	font-stretch: \${1};
snippet fst:c
	font-stretch: condensed;
snippet fst:e
	font-stretch: expanded;
snippet fst:ec
	font-stretch: extra-condensed;
snippet fst:ee
	font-stretch: extra-expanded;
snippet fst:n
	font-stretch: normal;
snippet fst:sc
	font-stretch: semi-condensed;
snippet fst:se
	font-stretch: semi-expanded;
snippet fst:uc
	font-stretch: ultra-condensed;
snippet fst:ue
	font-stretch: ultra-expanded;
snippet fs
	font-style: \${1};
snippet fs:i
	font-style: italic;
snippet fs:n
	font-style: normal;
snippet fs:o
	font-style: oblique;
snippet fv
	font-variant: \${1};
snippet fv:n
	font-variant: normal;
snippet fv:sc
	font-variant: small-caps;
snippet fw
	font-weight: \${1};
snippet fw:b
	font-weight: bold;
snippet fw:br
	font-weight: bolder;
snippet fw:lr
	font-weight: lighter;
snippet fw:n
	font-weight: normal;
snippet f
	font: \${1};
snippet h
	height: \${1};
snippet h:a
	height: auto;
snippet l
	left: \${1};
snippet l:a
	left: auto;
snippet lts
	letter-spacing: \${1};
snippet lh
	line-height: \${1};
snippet lisi
	list-style-image: url(\${1});
snippet lisi:n
	list-style-image: none;
snippet lisp
	list-style-position: \${1};
snippet lisp:i
	list-style-position: inside;
snippet lisp:o
	list-style-position: outside;
snippet list
	list-style-type: \${1};
snippet list:c
	list-style-type: circle;
snippet list:dclz
	list-style-type: decimal-leading-zero;
snippet list:dc
	list-style-type: decimal;
snippet list:d
	list-style-type: disc;
snippet list:lr
	list-style-type: lower-roman;
snippet list:n
	list-style-type: none;
snippet list:s
	list-style-type: square;
snippet list:ur
	list-style-type: upper-roman;
snippet lis
	list-style: \${1};
snippet lis:n
	list-style: none;
snippet mb
	margin-bottom: \${1};
snippet mb:a
	margin-bottom: auto;
snippet ml
	margin-left: \${1};
snippet ml:a
	margin-left: auto;
snippet mr
	margin-right: \${1};
snippet mr:a
	margin-right: auto;
snippet mt
	margin-top: \${1};
snippet mt:a
	margin-top: auto;
snippet m
	margin: \${1};
snippet m:4
	margin: \${1:0} \${2:0} \${3:0} \${4:0};
snippet m:3
	margin: \${1:0} \${2:0} \${3:0};
snippet m:2
	margin: \${1:0} \${2:0};
snippet m:0
	margin: 0;
snippet m:a
	margin: auto;
snippet mah
	max-height: \${1};
snippet mah:n
	max-height: none;
snippet maw
	max-width: \${1};
snippet maw:n
	max-width: none;
snippet mih
	min-height: \${1};
snippet miw
	min-width: \${1};
snippet op
	opacity: \${1};
snippet op:ie
	filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=\${1:100});
snippet op:ms
	-ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=\${1:100})';
snippet orp
	orphans: \${1};
snippet o+
	outline: \${1:1px} \${2:solid} #\${3:000};
snippet oc
	outline-color: \${1:#000};
snippet oc:i
	outline-color: invert;
snippet oo
	outline-offset: \${1};
snippet os
	outline-style: \${1};
snippet ow
	outline-width: \${1};
snippet o
	outline: \${1};
snippet o:n
	outline: none;
snippet ovs
	overflow-style: \${1};
snippet ovs:a
	overflow-style: auto;
snippet ovs:mq
	overflow-style: marquee;
snippet ovs:mv
	overflow-style: move;
snippet ovs:p
	overflow-style: panner;
snippet ovs:s
	overflow-style: scrollbar;
snippet ovx
	overflow-x: \${1};
snippet ovx:a
	overflow-x: auto;
snippet ovx:h
	overflow-x: hidden;
snippet ovx:s
	overflow-x: scroll;
snippet ovx:v
	overflow-x: visible;
snippet ovy
	overflow-y: \${1};
snippet ovy:a
	overflow-y: auto;
snippet ovy:h
	overflow-y: hidden;
snippet ovy:s
	overflow-y: scroll;
snippet ovy:v
	overflow-y: visible;
snippet ov
	overflow: \${1};
snippet ov:a
	overflow: auto;
snippet ov:h
	overflow: hidden;
snippet ov:s
	overflow: scroll;
snippet ov:v
	overflow: visible;
snippet pb
	padding-bottom: \${1};
snippet pl
	padding-left: \${1};
snippet pr
	padding-right: \${1};
snippet pt
	padding-top: \${1};
snippet p
	padding: \${1};
snippet p:4
	padding: \${1:0} \${2:0} \${3:0} \${4:0};
snippet p:3
	padding: \${1:0} \${2:0} \${3:0};
snippet p:2
	padding: \${1:0} \${2:0};
snippet p:0
	padding: 0;
snippet pgba
	page-break-after: \${1};
snippet pgba:aw
	page-break-after: always;
snippet pgba:a
	page-break-after: auto;
snippet pgba:l
	page-break-after: left;
snippet pgba:r
	page-break-after: right;
snippet pgbb
	page-break-before: \${1};
snippet pgbb:aw
	page-break-before: always;
snippet pgbb:a
	page-break-before: auto;
snippet pgbb:l
	page-break-before: left;
snippet pgbb:r
	page-break-before: right;
snippet pgbi
	page-break-inside: \${1};
snippet pgbi:a
	page-break-inside: auto;
snippet pgbi:av
	page-break-inside: avoid;
snippet pos
	position: \${1};
snippet pos:a
	position: absolute;
snippet pos:f
	position: fixed;
snippet pos:r
	position: relative;
snippet pos:s
	position: static;
snippet q
	quotes: \${1};
snippet q:en
	quotes: '\\201C' '\\201D' '\\2018' '\\2019';
snippet q:n
	quotes: none;
snippet q:ru
	quotes: '\\00AB' '\\00BB' '\\201E' '\\201C';
snippet rz
	resize: \${1};
snippet rz:b
	resize: both;
snippet rz:h
	resize: horizontal;
snippet rz:n
	resize: none;
snippet rz:v
	resize: vertical;
snippet r
	right: \${1};
snippet r:a
	right: auto;
snippet tbl
	table-layout: \${1};
snippet tbl:a
	table-layout: auto;
snippet tbl:f
	table-layout: fixed;
snippet tal
	text-align-last: \${1};
snippet tal:a
	text-align-last: auto;
snippet tal:c
	text-align-last: center;
snippet tal:l
	text-align-last: left;
snippet tal:r
	text-align-last: right;
snippet ta
	text-align: \${1};
snippet ta:c
	text-align: center;
snippet ta:l
	text-align: left;
snippet ta:r
	text-align: right;
snippet td
	text-decoration: \${1};
snippet td:l
	text-decoration: line-through;
snippet td:n
	text-decoration: none;
snippet td:o
	text-decoration: overline;
snippet td:u
	text-decoration: underline;
snippet te
	text-emphasis: \${1};
snippet te:ac
	text-emphasis: accent;
snippet te:a
	text-emphasis: after;
snippet te:b
	text-emphasis: before;
snippet te:c
	text-emphasis: circle;
snippet te:ds
	text-emphasis: disc;
snippet te:dt
	text-emphasis: dot;
snippet te:n
	text-emphasis: none;
snippet th
	text-height: \${1};
snippet th:a
	text-height: auto;
snippet th:f
	text-height: font-size;
snippet th:m
	text-height: max-size;
snippet th:t
	text-height: text-size;
snippet ti
	text-indent: \${1};
snippet ti:-
	text-indent: -9999px;
snippet tj
	text-justify: \${1};
snippet tj:a
	text-justify: auto;
snippet tj:d
	text-justify: distribute;
snippet tj:ic
	text-justify: inter-cluster;
snippet tj:ii
	text-justify: inter-ideograph;
snippet tj:iw
	text-justify: inter-word;
snippet tj:k
	text-justify: kashida;
snippet tj:t
	text-justify: tibetan;
snippet to+
	text-outline: \${1:0} \${2:0} #\${3:000};
snippet to
	text-outline: \${1};
snippet to:n
	text-outline: none;
snippet tr
	text-replace: \${1};
snippet tr:n
	text-replace: none;
snippet tsh+
	text-shadow: \${1:0} \${2:0} \${3:0} #\${4:000};
snippet tsh
	text-shadow: \${1};
snippet tsh:n
	text-shadow: none;
snippet tt
	text-transform: \${1};
snippet tt:c
	text-transform: capitalize;
snippet tt:l
	text-transform: lowercase;
snippet tt:n
	text-transform: none;
snippet tt:u
	text-transform: uppercase;
snippet tw
	text-wrap: \${1};
snippet tw:no
	text-wrap: none;
snippet tw:n
	text-wrap: normal;
snippet tw:s
	text-wrap: suppress;
snippet tw:u
	text-wrap: unrestricted;
snippet t
	top: \${1};
snippet t:a
	top: auto;
snippet va
	vertical-align: \${1};
snippet va:bl
	vertical-align: baseline;
snippet va:b
	vertical-align: bottom;
snippet va:m
	vertical-align: middle;
snippet va:sub
	vertical-align: sub;
snippet va:sup
	vertical-align: super;
snippet va:tb
	vertical-align: text-bottom;
snippet va:tt
	vertical-align: text-top;
snippet va:t
	vertical-align: top;
snippet v
	visibility: \${1};
snippet v:c
	visibility: collapse;
snippet v:h
	visibility: hidden;
snippet v:v
	visibility: visible;
snippet whsc
	white-space-collapse: \${1};
snippet whsc:ba
	white-space-collapse: break-all;
snippet whsc:bs
	white-space-collapse: break-strict;
snippet whsc:k
	white-space-collapse: keep-all;
snippet whsc:l
	white-space-collapse: loose;
snippet whsc:n
	white-space-collapse: normal;
snippet whs
	white-space: \${1};
snippet whs:n
	white-space: normal;
snippet whs:nw
	white-space: nowrap;
snippet whs:pl
	white-space: pre-line;
snippet whs:pw
	white-space: pre-wrap;
snippet whs:p
	white-space: pre;
snippet wid
	widows: \${1};
snippet w
	width: \${1};
snippet w:a
	width: auto;
snippet wob
	word-break: \${1};
snippet wob:ba
	word-break: break-all;
snippet wob:bs
	word-break: break-strict;
snippet wob:k
	word-break: keep-all;
snippet wob:l
	word-break: loose;
snippet wob:n
	word-break: normal;
snippet wos
	word-spacing: \${1};
snippet wow
	word-wrap: \${1};
snippet wow:no
	word-wrap: none;
snippet wow:n
	word-wrap: normal;
snippet wow:s
	word-wrap: suppress;
snippet wow:u
	word-wrap: unrestricted;
snippet z
	z-index: \${1};
snippet z:a
	z-index: auto;
snippet zoo
	zoom: 1;
`}),ace.define("ace/snippets/css",["require","exports","module","ace/snippets/css.snippets"],function(n,l,T){l.snippetText=n("./css.snippets"),l.scope="css"}),function(){ace.require(["ace/snippets/css"],function(n){k&&(k.exports=n)})}()})(si);var ai={exports:{}};(function(k,f){ace.define("ace/snippets/javascript.snippets",["require","exports","module"],function(n,l,T){T.exports=`# Prototype
snippet proto
	\${1:class_name}.prototype.\${2:method_name} = function(\${3:first_argument}) {
		\${4:// body...}
	};
# Function
snippet fun
	function \${1?:function_name}(\${2:argument}) {
		\${3:// body...}
	}
# Anonymous Function
regex /((=)\\s*|(:)\\s*|(\\()|\\b)/f/(\\))?/
snippet f
	function\${M1?: \${1:functionName}}($2) {
		\${0:$TM_SELECTED_TEXT}
	}\${M2?;}\${M3?,}\${M4?)}
# Immediate function
trigger \\(?f\\(
endTrigger \\)?
snippet f(
	(function(\${1}) {
		\${0:\${TM_SELECTED_TEXT:/* code */}}
	}(\${1}));
# if
snippet if
	if (\${1:true}) {
		\${0}
	}
# if ... else
snippet ife
	if (\${1:true}) {
		\${2}
	} else {
		\${0}
	}
# tertiary conditional
snippet ter
	\${1:/* condition */} ? \${2:a} : \${3:b}
# switch
snippet switch
	switch (\${1:expression}) {
		case '\${3:case}':
			\${4:// code}
			break;
		\${5}
		default:
			\${2:// code}
	}
# case
snippet case
	case '\${1:case}':
		\${2:// code}
		break;
	\${3}

# while (...) {...}
snippet wh
	while (\${1:/* condition */}) {
		\${0:/* code */}
	}
# try
snippet try
	try {
		\${0:/* code */}
	} catch (e) {}
# do...while
snippet do
	do {
		\${2:/* code */}
	} while (\${1:/* condition */});
# Object Method
snippet :f
regex /([,{[])|^\\s*/:f/
	\${1:method_name}: function(\${2:attribute}) {
		\${0}
	}\${3:,}
# setTimeout function
snippet setTimeout
regex /\\b/st|timeout|setTimeo?u?t?/
	setTimeout(function() {\${3:$TM_SELECTED_TEXT}}, \${1:10});
# Get Elements
snippet gett
	getElementsBy\${1:TagName}('\${2}')\${3}
# Get Element
snippet get
	getElementBy\${1:Id}('\${2}')\${3}
# console.log (Firebug)
snippet cl
	console.log(\${1});
# return
snippet ret
	return \${1:result}
# for (property in object ) { ... }
snippet fori
	for (var \${1:prop} in \${2:Things}) {
		\${0:$2[$1]}
	}
# hasOwnProperty
snippet has
	hasOwnProperty(\${1})
# docstring
snippet /**
	/**
	 * \${1:description}
	 *
	 */
snippet @par
regex /^\\s*\\*\\s*/@(para?m?)?/
	@param {\${1:type}} \${2:name} \${3:description}
snippet @ret
	@return {\${1:type}} \${2:description}
# JSON.parse
snippet jsonp
	JSON.parse(\${1:jstr});
# JSON.stringify
snippet jsons
	JSON.stringify(\${1:object});
# self-defining function
snippet sdf
	var \${1:function_name} = function(\${2:argument}) {
		\${3:// initial code ...}

		$1 = function($2) {
			\${4:// main code}
		};
	}
# singleton
snippet sing
	function \${1:Singleton} (\${2:argument}) {
		// the cached instance
		var instance;

		// rewrite the constructor
		$1 = function $1($2) {
			return instance;
		};
		
		// carry over the prototype properties
		$1.prototype = this;

		// the instance
		instance = new $1();

		// reset the constructor pointer
		instance.constructor = $1;

		\${3:// code ...}

		return instance;
	}
# class
snippet class
regex /^\\s*/clas{0,2}/
	var \${1:class} = function(\${20}) {
		$40$0
	};
	
	(function() {
		\${60:this.prop = ""}
	}).call(\${1:class}.prototype);
	
	exports.\${1:class} = \${1:class};
# 
snippet for-
	for (var \${1:i} = \${2:Things}.length; \${1:i}--; ) {
		\${0:\${2:Things}[\${1:i}];}
	}
# for (...) {...}
snippet for
	for (var \${1:i} = 0; $1 < \${2:Things}.length; $1++) {
		\${3:$2[$1]}$0
	}
# for (...) {...} (Improved Native For-Loop)
snippet forr
	for (var \${1:i} = \${2:Things}.length - 1; $1 >= 0; $1--) {
		\${3:$2[$1]}$0
	}


#modules
snippet def
	define(function(require, exports, module) {
	"use strict";
	var \${1/.*\\///} = require("\${1}");
	
	$TM_SELECTED_TEXT
	});
snippet req
guard ^\\s*
	var \${1/.*\\///} = require("\${1}");
	$0
snippet requ
guard ^\\s*
	var \${1/.*\\/(.)/\\u$1/} = require("\${1}").\${1/.*\\/(.)/\\u$1/};
	$0
`}),ace.define("ace/snippets/javascript",["require","exports","module","ace/snippets/javascript.snippets"],function(n,l,T){l.snippetText=n("./javascript.snippets"),l.scope="javascript"}),function(){ace.require(["ace/snippets/javascript"],function(n){k&&(k.exports=n)})}()})(ai);var li={exports:{}};(function(k,f){ace.define("ace/theme/tomorrow-css",["require","exports","module"],function(n,l,T){T.exports=`.ace-tomorrow .ace_gutter {
  background: #f6f6f6;
  color: #4D4D4C
}

.ace-tomorrow .ace_print-margin {
  width: 1px;
  background: #f6f6f6
}

.ace-tomorrow {
  background-color: #FFFFFF;
  color: #4D4D4C
}

.ace-tomorrow .ace_cursor {
  color: #AEAFAD
}

.ace-tomorrow .ace_marker-layer .ace_selection {
  background: #D6D6D6
}

.ace-tomorrow.ace_multiselect .ace_selection.ace_start {
  box-shadow: 0 0 3px 0px #FFFFFF;
}

.ace-tomorrow .ace_marker-layer .ace_step {
  background: rgb(255, 255, 0)
}

.ace-tomorrow .ace_marker-layer .ace_bracket {
  margin: -1px 0 0 -1px;
  border: 1px solid #D1D1D1
}

.ace-tomorrow .ace_marker-layer .ace_active-line {
  background: #EFEFEF
}

.ace-tomorrow .ace_gutter-active-line {
  background-color : #dcdcdc
}

.ace-tomorrow .ace_marker-layer .ace_selected-word {
  border: 1px solid #D6D6D6
}

.ace-tomorrow .ace_invisible {
  color: #D1D1D1
}

.ace-tomorrow .ace_keyword,
.ace-tomorrow .ace_meta,
.ace-tomorrow .ace_storage,
.ace-tomorrow .ace_storage.ace_type,
.ace-tomorrow .ace_support.ace_type {
  color: #8959A8
}

.ace-tomorrow .ace_keyword.ace_operator {
  color: #3E999F
}

.ace-tomorrow .ace_constant.ace_character,
.ace-tomorrow .ace_constant.ace_language,
.ace-tomorrow .ace_constant.ace_numeric,
.ace-tomorrow .ace_keyword.ace_other.ace_unit,
.ace-tomorrow .ace_support.ace_constant,
.ace-tomorrow .ace_variable.ace_parameter {
  color: #F5871F
}

.ace-tomorrow .ace_constant.ace_other {
  color: #666969
}

.ace-tomorrow .ace_invalid {
  color: #FFFFFF;
  background-color: #C82829
}

.ace-tomorrow .ace_invalid.ace_deprecated {
  color: #FFFFFF;
  background-color: #8959A8
}

.ace-tomorrow .ace_fold {
  background-color: #4271AE;
  border-color: #4D4D4C
}

.ace-tomorrow .ace_entity.ace_name.ace_function,
.ace-tomorrow .ace_support.ace_function,
.ace-tomorrow .ace_variable {
  color: #4271AE
}

.ace-tomorrow .ace_support.ace_class,
.ace-tomorrow .ace_support.ace_type {
  color: #C99E00
}

.ace-tomorrow .ace_heading,
.ace-tomorrow .ace_markup.ace_heading,
.ace-tomorrow .ace_string {
  color: #718C00
}

.ace-tomorrow .ace_entity.ace_name.ace_tag,
.ace-tomorrow .ace_entity.ace_other.ace_attribute-name,
.ace-tomorrow .ace_meta.ace_tag,
.ace-tomorrow .ace_string.ace_regexp,
.ace-tomorrow .ace_variable {
  color: #C82829
}

.ace-tomorrow .ace_comment {
  color: #8E908C
}

.ace-tomorrow .ace_indent-guide {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bdu3f/BwAlfgctduB85QAAAABJRU5ErkJggg==) right repeat-y
}

.ace-tomorrow .ace_indent-guide-active {
  background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAAZSURBVHjaYvj///9/hivKyv8BAAAA//8DACLqBhbvk+/eAAAAAElFTkSuQmCC") right repeat-y;
} 
`}),ace.define("ace/theme/tomorrow",["require","exports","module","ace/theme/tomorrow-css","ace/lib/dom"],function(n,l,T){l.isDark=!1,l.cssClass="ace-tomorrow",l.cssText=n("./tomorrow-css");var e=n("../lib/dom");e.importCssString(l.cssText,l.cssClass,!1)}),function(){ace.require(["ace/theme/tomorrow"],function(n){k&&(k.exports=n)})}()})(li);var ci={exports:{}};(function(k,f){ace.define("ace/theme/twilight-css",["require","exports","module"],function(n,l,T){T.exports=`.ace-twilight .ace_gutter {
  background: #232323;
  color: #E2E2E2
}

.ace-twilight .ace_print-margin {
  width: 1px;
  background: #232323
}

.ace-twilight {
  background-color: #141414;
  color: #F8F8F8
}

.ace-twilight .ace_cursor {
  color: #A7A7A7
}

.ace-twilight .ace_marker-layer .ace_selection {
  background: rgba(221, 240, 255, 0.20)
}

.ace-twilight.ace_multiselect .ace_selection.ace_start {
  box-shadow: 0 0 3px 0px #141414;
}

.ace-twilight .ace_marker-layer .ace_step {
  background: rgb(102, 82, 0)
}

.ace-twilight .ace_marker-layer .ace_bracket {
  margin: -1px 0 0 -1px;
  border: 1px solid rgba(255, 255, 255, 0.25)
}

.ace-twilight .ace_marker-layer .ace_active-line {
  background: rgba(255, 255, 255, 0.031)
}

.ace-twilight .ace_gutter-active-line {
  background-color: rgba(255, 255, 255, 0.031)
}

.ace-twilight .ace_marker-layer .ace_selected-word {
  border: 1px solid rgba(221, 240, 255, 0.20)
}

.ace-twilight .ace_invisible {
  color: rgba(255, 255, 255, 0.25)
}

.ace-twilight .ace_keyword,
.ace-twilight .ace_meta {
  color: #CDA869
}

.ace-twilight .ace_constant,
.ace-twilight .ace_constant.ace_character,
.ace-twilight .ace_constant.ace_character.ace_escape,
.ace-twilight .ace_constant.ace_other,
.ace-twilight .ace_heading,
.ace-twilight .ace_markup.ace_heading,
.ace-twilight .ace_support.ace_constant {
  color: #CF6A4C
}

.ace-twilight .ace_invalid.ace_illegal {
  color: #F8F8F8;
  background-color: rgba(86, 45, 86, 0.75)
}

.ace-twilight .ace_invalid.ace_deprecated {
  text-decoration: underline;
  font-style: italic;
  color: #D2A8A1
}

.ace-twilight .ace_support {
  color: #9B859D
}

.ace-twilight .ace_fold {
  background-color: #AC885B;
  border-color: #F8F8F8
}

.ace-twilight .ace_support.ace_function {
  color: #DAD085
}

.ace-twilight .ace_list,
.ace-twilight .ace_markup.ace_list,
.ace-twilight .ace_storage {
  color: #F9EE98
}

.ace-twilight .ace_entity.ace_name.ace_function,
.ace-twilight .ace_meta.ace_tag {
  color: #AC885B
}

.ace-twilight .ace_string {
  color: #8F9D6A
}

.ace-twilight .ace_string.ace_regexp {
  color: #E9C062
}

.ace-twilight .ace_comment {
  font-style: italic;
  color: #5F5A60
}

.ace-twilight .ace_variable {
  color: #7587A6
}

.ace-twilight .ace_xml-pe {
  color: #494949
}

.ace-twilight .ace_indent-guide {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAEklEQVQImWMQERFpYLC1tf0PAAgOAnPnhxyiAAAAAElFTkSuQmCC) right repeat-y
}

.ace-twilight .ace_indent-guide-active {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAEklEQVQIW2PQ1dX9zzBz5sz/ABCcBFFentLlAAAAAElFTkSuQmCC) right repeat-y;
}
`}),ace.define("ace/theme/twilight",["require","exports","module","ace/theme/twilight-css","ace/lib/dom"],function(n,l,T){l.isDark=!0,l.cssClass="ace-twilight",l.cssText=n("./twilight-css");var e=n("../lib/dom");e.importCssString(l.cssText,l.cssClass,!1)}),function(){ace.require(["ace/theme/twilight"],function(n){k&&(k.exports=n)})}()})(ci);var pi={exports:{}};(function(k,f){ace.define("ace/snippets",["require","exports","module","ace/lib/dom","ace/lib/oop","ace/lib/event_emitter","ace/lib/lang","ace/range","ace/range_list","ace/keyboard/hash_handler","ace/tokenizer","ace/clipboard","ace/editor"],function(n,l,T){function e(t){var i=new Date().toLocaleString("en-us",t);return i.length==1?"0"+i:i}var a=n("./lib/dom"),u=n("./lib/oop"),g=n("./lib/event_emitter").EventEmitter,p=n("./lib/lang"),s=n("./range").Range,o=n("./range_list").RangeList,d=n("./keyboard/hash_handler").HashHandler,m=n("./tokenizer").Tokenizer,_=n("./clipboard"),C={CURRENT_WORD:function(t){return t.session.getTextRange(t.session.getWordRange())},SELECTION:function(t,i,r){var c=t.session.getTextRange();return r?c.replace(/\n\r?([ \t]*\S)/g,`
`+r+"$1"):c},CURRENT_LINE:function(t){return t.session.getLine(t.getCursorPosition().row)},PREV_LINE:function(t){return t.session.getLine(t.getCursorPosition().row-1)},LINE_INDEX:function(t){return t.getCursorPosition().row},LINE_NUMBER:function(t){return t.getCursorPosition().row+1},SOFT_TABS:function(t){return t.session.getUseSoftTabs()?"YES":"NO"},TAB_SIZE:function(t){return t.session.getTabSize()},CLIPBOARD:function(t){return _.getText&&_.getText()},FILENAME:function(t){return/[^/\\]*$/.exec(this.FILEPATH(t))[0]},FILENAME_BASE:function(t){return/[^/\\]*$/.exec(this.FILEPATH(t))[0].replace(/\.[^.]*$/,"")},DIRECTORY:function(t){return this.FILEPATH(t).replace(/[^/\\]*$/,"")},FILEPATH:function(t){return"/not implemented.txt"},WORKSPACE_NAME:function(){return"Unknown"},FULLNAME:function(){return"Unknown"},BLOCK_COMMENT_START:function(t){var i=t.session.$mode||{};return i.blockComment&&i.blockComment.start||""},BLOCK_COMMENT_END:function(t){var i=t.session.$mode||{};return i.blockComment&&i.blockComment.end||""},LINE_COMMENT:function(t){var i=t.session.$mode||{};return i.lineCommentStart||""},CURRENT_YEAR:e.bind(null,{year:"numeric"}),CURRENT_YEAR_SHORT:e.bind(null,{year:"2-digit"}),CURRENT_MONTH:e.bind(null,{month:"numeric"}),CURRENT_MONTH_NAME:e.bind(null,{month:"long"}),CURRENT_MONTH_NAME_SHORT:e.bind(null,{month:"short"}),CURRENT_DATE:e.bind(null,{day:"2-digit"}),CURRENT_DAY_NAME:e.bind(null,{weekday:"long"}),CURRENT_DAY_NAME_SHORT:e.bind(null,{weekday:"short"}),CURRENT_HOUR:e.bind(null,{hour:"2-digit",hour12:!1}),CURRENT_MINUTE:e.bind(null,{minute:"2-digit"}),CURRENT_SECOND:e.bind(null,{second:"2-digit"})};C.SELECTED_TEXT=C.SELECTION;var b=function(){function t(){this.snippetMap={},this.snippetNameMap={},this.variables=C}return t.prototype.getTokenizer=function(){return t.$tokenizer||this.createTokenizer()},t.prototype.createTokenizer=function(){function i(h){return h=h.substr(1),/^\d+$/.test(h)?[{tabstopId:parseInt(h,10)}]:[{text:h}]}function r(h){return"(?:[^\\\\"+h+"]|\\\\.)"}var c={regex:"/("+r("/")+"+)/",onMatch:function(h,x,w){var $=w[0];return $.fmtString=!0,$.guard=h.slice(1,-1),$.flag="",""},next:"formatString"};return t.$tokenizer=new m({start:[{regex:/\\./,onMatch:function(h,x,w){var $=h[1];return($=="}"&&w.length||"`$\\".indexOf($)!=-1)&&(h=$),[h]}},{regex:/}/,onMatch:function(h,x,w){return[w.length?w.shift():h]}},{regex:/\$(?:\d+|\w+)/,onMatch:i},{regex:/\$\{[\dA-Z_a-z]+/,onMatch:function(h,x,w){var $=i(h.substr(1));return w.unshift($[0]),$},next:"snippetVar"},{regex:/\n/,token:"newline",merge:!1}],snippetVar:[{regex:"\\|"+r("\\|")+"*\\|",onMatch:function(h,x,w){var $=h.slice(1,-1).replace(/\\[,|\\]|,/g,function(E){return E.length==2?E[1]:"\0"}).split("\0").map(function(E){return{value:E}});return w[0].choices=$,[$[0]]},next:"start"},c,{regex:"([^:}\\\\]|\\\\.)*:?",token:"",next:"start"}],formatString:[{regex:/:/,onMatch:function(h,x,w){return w.length&&w[0].expectElse?(w[0].expectElse=!1,w[0].ifEnd={elseEnd:w[0]},[w[0].ifEnd]):":"}},{regex:/\\./,onMatch:function(h,x,w){var $=h[1];return $=="}"&&w.length||"`$\\".indexOf($)!=-1?h=$:$=="n"?h=`
`:$=="t"?h="	":"ulULE".indexOf($)!=-1&&(h={changeCase:$,local:$>"a"}),[h]}},{regex:"/\\w*}",onMatch:function(h,x,w){var $=w.shift();return $&&($.flag=h.slice(1,-1)),this.next=$&&$.tabstopId?"start":"",[$||h]},next:"start"},{regex:/\$(?:\d+|\w+)/,onMatch:function(h,x,w){return[{text:h.slice(1)}]}},{regex:/\${\w+/,onMatch:function(h,x,w){var $={text:h.slice(2)};return w.unshift($),[$]},next:"formatStringVar"},{regex:/\n/,token:"newline",merge:!1},{regex:/}/,onMatch:function(h,x,w){var $=w.shift();return this.next=$&&$.tabstopId?"start":"",[$||h]},next:"start"}],formatStringVar:[{regex:/:\/\w+}/,onMatch:function(h,x,w){var $=w[0];return $.formatFunction=h.slice(2,-1),[w.shift()]},next:"formatString"},c,{regex:/:[\?\-+]?/,onMatch:function(h,x,w){h[1]=="+"&&(w[0].ifEnd=w[0]),h[1]=="?"&&(w[0].expectElse=!0)},next:"formatString"},{regex:"([^:}\\\\]|\\\\.)*:?",token:"",next:"formatString"}]}),t.$tokenizer},t.prototype.tokenizeTmSnippet=function(i,r){return this.getTokenizer().getLineTokens(i,r).tokens.map(function(c){return c.value||c})},t.prototype.getVariableValue=function(i,r,c){if(/^\d+$/.test(r))return(this.variables.__||{})[r]||"";if(/^[A-Z]\d+$/.test(r))return(this.variables[r[0]+"__"]||{})[r.substr(1)]||"";if(r=r.replace(/^TM_/,""),!this.variables.hasOwnProperty(r))return"";var h=this.variables[r];return typeof h=="function"&&(h=this.variables[r](i,r,c)),h==null?"":h},t.prototype.tmStrFormat=function(i,r,c){if(!r.fmt)return i;var h=r.flag||"",x=r.guard;x=new RegExp(x,h.replace(/[^gim]/g,""));var w=typeof r.fmt=="string"?this.tokenizeTmSnippet(r.fmt,"formatString"):r.fmt,$=this,E=i.replace(x,function(){var M=$.variables.__;$.variables.__=[].slice.call(arguments);for(var L=$.resolveVariables(w,c),N="E",P=0;P<L.length;P++){var z=L[P];if(typeof z=="object")if(L[P]="",z.changeCase&&z.local){var j=L[P+1];j&&typeof j=="string"&&(z.changeCase=="u"?L[P]=j[0].toUpperCase():L[P]=j[0].toLowerCase(),L[P+1]=j.substr(1))}else z.changeCase&&(N=z.changeCase);else N=="U"?L[P]=z.toUpperCase():N=="L"&&(L[P]=z.toLowerCase())}return $.variables.__=M,L.join("")});return E},t.prototype.tmFormatFunction=function(i,r,c){return r.formatFunction=="upcase"?i.toUpperCase():r.formatFunction=="downcase"?i.toLowerCase():i},t.prototype.resolveVariables=function(i,r){function c(N){var P=i.indexOf(N,$+1);P!=-1&&($=P)}for(var h=[],x="",w=!0,$=0;$<i.length;$++){var E=i[$];if(typeof E=="string"){h.push(E),E==`
`?(w=!0,x=""):w&&(x=/^\t*/.exec(E)[0],w=/\S/.test(E));continue}if(E){if(w=!1,E.fmtString){var M=i.indexOf(E,$+1);M==-1&&(M=i.length),E.fmt=i.slice($+1,M),$=M}if(E.text){var L=this.getVariableValue(r,E.text,x)+"";E.fmtString&&(L=this.tmStrFormat(L,E,r)),E.formatFunction&&(L=this.tmFormatFunction(L,E,r)),L&&!E.ifEnd?(h.push(L),c(E)):!L&&E.ifEnd&&c(E.ifEnd)}else E.elseEnd?c(E.elseEnd):(E.tabstopId!=null||E.changeCase!=null)&&h.push(E)}}return h},t.prototype.getDisplayTextForSnippet=function(i,r){var c=y.call(this,i,r);return c.text},t.prototype.insertSnippetForSelection=function(i,r,c){c===void 0&&(c={});var h=y.call(this,i,r,c),x=i.getSelectionRange(),w=i.session.replace(x,h.text),$=new A(i),E=i.inVirtualSelectionMode&&i.selection.index;$.addTabstops(h.tabstops,x.start,w,E)},t.prototype.insertSnippet=function(i,r,c){c===void 0&&(c={});var h=this;if(i.inVirtualSelectionMode)return h.insertSnippetForSelection(i,r,c);i.forEachSelection(function(){h.insertSnippetForSelection(i,r,c)},null,{keepOrder:!0}),i.tabstopManager&&i.tabstopManager.tabNext()},t.prototype.$getScope=function(i){var r=i.session.$mode.$id||"";if(r=r.split("/").pop(),r==="html"||r==="php"){r==="php"&&!i.session.$mode.inlinePhp&&(r="html");var c=i.getCursorPosition(),h=i.session.getState(c.row);typeof h=="object"&&(h=h[0]),h.substring&&(h.substring(0,3)=="js-"?r="javascript":h.substring(0,4)=="css-"?r="css":h.substring(0,4)=="php-"&&(r="php"))}return r},t.prototype.getActiveScopes=function(i){var r=this.$getScope(i),c=[r],h=this.snippetMap;return h[r]&&h[r].includeScopes&&c.push.apply(c,h[r].includeScopes),c.push("_"),c},t.prototype.expandWithTab=function(i,r){var c=this,h=i.forEachSelection(function(){return c.expandSnippetForSelection(i,r)},null,{keepOrder:!0});return h&&i.tabstopManager&&i.tabstopManager.tabNext(),h},t.prototype.expandSnippetForSelection=function(i,r){var c=i.getCursorPosition(),h=i.session.getLine(c.row),x=h.substring(0,c.column),w=h.substr(c.column),$=this.snippetMap,E;return this.getActiveScopes(i).some(function(M){var L=$[M];return L&&(E=this.findMatchingSnippet(L,x,w)),!!E},this),E?(r&&r.dryRun||(i.session.doc.removeInLine(c.row,c.column-E.replaceBefore.length,c.column+E.replaceAfter.length),this.variables.M__=E.matchBefore,this.variables.T__=E.matchAfter,this.insertSnippetForSelection(i,E.content),this.variables.M__=this.variables.T__=null),!0):!1},t.prototype.findMatchingSnippet=function(i,r,c){for(var h=i.length;h--;){var x=i[h];if(!(x.startRe&&!x.startRe.test(r))&&!(x.endRe&&!x.endRe.test(c))&&!(!x.startRe&&!x.endRe))return x.matchBefore=x.startRe?x.startRe.exec(r):[""],x.matchAfter=x.endRe?x.endRe.exec(c):[""],x.replaceBefore=x.triggerRe?x.triggerRe.exec(r)[0]:"",x.replaceAfter=x.endTriggerRe?x.endTriggerRe.exec(c)[0]:"",x}},t.prototype.register=function(i,r){function c(M){return M&&!/^\^?\(.*\)\$?$|^\\b$/.test(M)&&(M="(?:"+M+")"),M||""}function h(M,L,N){return M=c(M),L=c(L),N?(M=L+M,M&&M[M.length-1]!="$"&&(M+="$")):(M+=L,M&&M[0]!="^"&&(M="^"+M)),new RegExp(M)}function x(M){M.scope||(M.scope=r||"_"),r=M.scope,w[r]||(w[r]=[],$[r]={});var L=$[r];if(M.name){var N=L[M.name];N&&E.unregister(N),L[M.name]=M}w[r].push(M),M.prefix&&(M.tabTrigger=M.prefix),!M.content&&M.body&&(M.content=Array.isArray(M.body)?M.body.join(`
`):M.body),M.tabTrigger&&!M.trigger&&(!M.guard&&/^\w/.test(M.tabTrigger)&&(M.guard="\\b"),M.trigger=p.escapeRegExp(M.tabTrigger)),!(!M.trigger&&!M.guard&&!M.endTrigger&&!M.endGuard)&&(M.startRe=h(M.trigger,M.guard,!0),M.triggerRe=new RegExp(M.trigger),M.endRe=h(M.endTrigger,M.endGuard,!0),M.endTriggerRe=new RegExp(M.endTrigger))}var w=this.snippetMap,$=this.snippetNameMap,E=this;i||(i=[]),Array.isArray(i)?i.forEach(x):Object.keys(i).forEach(function(M){x(i[M])}),this._signal("registerSnippets",{scope:r})},t.prototype.unregister=function(i,r){function c(w){var $=x[w.scope||r];if($&&$[w.name]){delete $[w.name];var E=h[w.scope||r],M=E&&E.indexOf(w);M>=0&&E.splice(M,1)}}var h=this.snippetMap,x=this.snippetNameMap;i.content?c(i):Array.isArray(i)&&i.forEach(c)},t.prototype.parseSnippetFile=function(i){i=i.replace(/\r/g,"");for(var r=[],c={},h=/^#.*|^({[\s\S]*})\s*$|^(\S+) (.*)$|^((?:\n*\t.*)+)/gm,x;x=h.exec(i);){if(x[1])try{c=JSON.parse(x[1]),r.push(c)}catch(M){}if(x[4])c.content=x[4].replace(/^\t/gm,""),r.push(c),c={};else{var w=x[2],$=x[3];if(w=="regex"){var E=/\/((?:[^\/\\]|\\.)*)|$/g;c.guard=E.exec($)[1],c.trigger=E.exec($)[1],c.endTrigger=E.exec($)[1],c.endGuard=E.exec($)[1]}else w=="snippet"?(c.tabTrigger=$.match(/^\S*/)[0],c.name||(c.name=$)):w&&(c[w]=$)}}return r},t.prototype.getSnippetByName=function(i,r){var c=this.snippetNameMap,h;return this.getActiveScopes(r).some(function(x){var w=c[x];return w&&(h=w[i]),!!h},this),h},t}();u.implement(b.prototype,g);var y=function(t,i,r){function c(D){for(var X=[],J=0;J<D.length;J++){var U=D[J];if(typeof U=="object"){if(L[U.tabstopId])continue;var ue=D.lastIndexOf(U,J-1);U=X[ue]||{tabstopId:U.tabstopId}}X[J]=U}return X}r===void 0&&(r={});var h=t.getCursorPosition(),x=t.session.getLine(h.row),w=t.session.getTabString(),$=x.match(/^\s*/)[0];h.column<$.length&&($=$.slice(0,h.column)),i=i.replace(/\r/g,"");var E=this.tokenizeTmSnippet(i);E=this.resolveVariables(E,t),E=E.map(function(D){return D==`
`&&!r.excludeExtraIndent?D+$:typeof D=="string"?D.replace(/\t/g,w):D});var M=[];E.forEach(function(D,X){if(typeof D=="object"){var J=D.tabstopId,U=M[J];if(U||(U=M[J]=[],U.index=J,U.value="",U.parents={}),U.indexOf(D)===-1){D.choices&&!U.choices&&(U.choices=D.choices),U.push(D);var ue=E.indexOf(D,X+1);if(ue!==-1){var de=E.slice(X+1,ue),Ce=de.some(function(Fe){return typeof Fe=="object"});Ce&&!U.value?U.value=de:de.length&&(!U.value||typeof U.value!="string")&&(U.value=de.join(""))}}}}),M.forEach(function(D){D.length=0});for(var L={},N=0;N<E.length;N++){var P=E[N];if(typeof P=="object"){var z=P.tabstopId,j=M[z],V=E.indexOf(P,N+1);if(L[z]){L[z]===P&&(delete L[z],Object.keys(L).forEach(function(D){j.parents[D]=!0}));continue}L[z]=P;var ee=j.value;typeof ee!="string"?ee=c(ee):P.fmt&&(ee=this.tmStrFormat(ee,P,t)),E.splice.apply(E,[N+1,Math.max(0,V-N)].concat(ee,P)),j.indexOf(P)===-1&&j.push(P)}}var Z=0,ie=0,K="";return E.forEach(function(D){if(typeof D=="string"){var X=D.split(`
`);X.length>1?(ie=X[X.length-1].length,Z+=X.length-1):ie+=D.length,K+=D}else D&&(D.start?D.end={row:Z,column:ie}:D.start={row:Z,column:ie})}),{text:K,tabstops:M,tokens:E}},A=function(){function t(i){if(this.index=0,this.ranges=[],this.tabstops=[],i.tabstopManager)return i.tabstopManager;i.tabstopManager=this,this.$onChange=this.onChange.bind(this),this.$onChangeSelection=p.delayedCall(this.onChangeSelection.bind(this)).schedule,this.$onChangeSession=this.onChangeSession.bind(this),this.$onAfterExec=this.onAfterExec.bind(this),this.attach(i)}return t.prototype.attach=function(i){this.$openTabstops=null,this.selectedTabstop=null,this.editor=i,this.session=i.session,this.editor.on("change",this.$onChange),this.editor.on("changeSelection",this.$onChangeSelection),this.editor.on("changeSession",this.$onChangeSession),this.editor.commands.on("afterExec",this.$onAfterExec),this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler)},t.prototype.detach=function(){this.tabstops.forEach(this.removeTabstopMarkers,this),this.ranges.length=0,this.tabstops.length=0,this.selectedTabstop=null,this.editor.off("change",this.$onChange),this.editor.off("changeSelection",this.$onChangeSelection),this.editor.off("changeSession",this.$onChangeSession),this.editor.commands.off("afterExec",this.$onAfterExec),this.editor.keyBinding.removeKeyboardHandler(this.keyboardHandler),this.editor.tabstopManager=null,this.session=null,this.editor=null},t.prototype.onChange=function(i){for(var r=i.action[0]=="r",c=this.selectedTabstop||{},h=c.parents||{},x=this.tabstops.slice(),w=0;w<x.length;w++){var $=x[w],E=$==c||h[$.index];if($.rangeList.$bias=E?0:1,i.action=="remove"&&$!==c){var M=$.parents&&$.parents[c.index],L=$.rangeList.pointIndex(i.start,M);L=L<0?-L-1:L+1;var N=$.rangeList.pointIndex(i.end,M);N=N<0?-N-1:N-1;for(var P=$.rangeList.ranges.slice(L,N),z=0;z<P.length;z++)this.removeRange(P[z])}$.rangeList.$onChange(i)}var j=this.session;!this.$inChange&&r&&j.getLength()==1&&!j.getValue()&&this.detach()},t.prototype.updateLinkedFields=function(){var i=this.selectedTabstop;if(!(!i||!i.hasLinkedRanges||!i.firstNonLinked)){this.$inChange=!0;for(var r=this.session,c=r.getTextRange(i.firstNonLinked),h=0;h<i.length;h++){var x=i[h];if(x.linked){var w=x.original,$=l.snippetManager.tmStrFormat(c,w,this.editor);r.replace(x,$)}}this.$inChange=!1}},t.prototype.onAfterExec=function(i){i.command&&!i.command.readOnly&&this.updateLinkedFields()},t.prototype.onChangeSelection=function(){if(this.editor){for(var i=this.editor.selection.lead,r=this.editor.selection.anchor,c=this.editor.selection.isEmpty(),h=0;h<this.ranges.length;h++)if(!this.ranges[h].linked){var x=this.ranges[h].contains(i.row,i.column),w=c||this.ranges[h].contains(r.row,r.column);if(x&&w)return}this.detach()}},t.prototype.onChangeSession=function(){this.detach()},t.prototype.tabNext=function(i){var r=this.tabstops.length,c=this.index+(i||1);c=Math.min(Math.max(c,1),r),c==r&&(c=0),this.selectTabstop(c),this.updateTabstopMarkers(),c===0&&this.detach()},t.prototype.selectTabstop=function(i){this.$openTabstops=null;var r=this.tabstops[this.index];if(r&&this.addTabstopMarkers(r),this.index=i,r=this.tabstops[this.index],!(!r||!r.length)){this.selectedTabstop=r;var c=r.firstNonLinked||r;if(r.choices&&(c.cursor=c.start),this.editor.inVirtualSelectionMode)this.editor.selection.fromOrientedRange(c);else{var h=this.editor.multiSelect;h.toSingleRange(c);for(var x=0;x<r.length;x++)r.hasLinkedRanges&&r[x].linked||h.addRange(r[x].clone(),!0)}this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler),this.selectedTabstop&&this.selectedTabstop.choices&&this.editor.execCommand("startAutocomplete",{matches:this.selectedTabstop.choices})}},t.prototype.addTabstops=function(i,r,c){var h=this.useLink||!this.editor.getOption("enableMultiselect");if(this.$openTabstops||(this.$openTabstops=[]),!i[0]){var x=s.fromPoints(c,c);R(x.start,r),R(x.end,r),i[0]=[x],i[0].index=0}var w=this.index,$=[w+1,0],E=this.ranges,M=this.snippetId=(this.snippetId||0)+1;i.forEach(function(L,N){var P=this.$openTabstops[N]||L;P.snippetId=M;for(var z=0;z<L.length;z++){var j=L[z],V=s.fromPoints(j.start,j.end||j.start);I(V.start,r),I(V.end,r),V.original=j,V.tabstop=P,E.push(V),P!=L?P.unshift(V):P[z]=V,j.fmtString||P.firstNonLinked&&h?(V.linked=!0,P.hasLinkedRanges=!0):P.firstNonLinked||(P.firstNonLinked=V)}P.firstNonLinked||(P.hasLinkedRanges=!1),P===L&&($.push(P),this.$openTabstops[N]=P),this.addTabstopMarkers(P),P.rangeList=P.rangeList||new o,P.rangeList.$bias=0,P.rangeList.addList(P)},this),$.length>2&&(this.tabstops.length&&$.push($.splice(2,1)[0]),this.tabstops.splice.apply(this.tabstops,$))},t.prototype.addTabstopMarkers=function(i){var r=this.session;i.forEach(function(c){c.markerId||(c.markerId=r.addMarker(c,"ace_snippet-marker","text"))})},t.prototype.removeTabstopMarkers=function(i){var r=this.session;i.forEach(function(c){r.removeMarker(c.markerId),c.markerId=null})},t.prototype.updateTabstopMarkers=function(){if(this.selectedTabstop){var i=this.selectedTabstop.snippetId;this.selectedTabstop.index===0&&i--,this.tabstops.forEach(function(r){r.snippetId===i?this.addTabstopMarkers(r):this.removeTabstopMarkers(r)},this)}},t.prototype.removeRange=function(i){var r=i.tabstop.indexOf(i);r!=-1&&i.tabstop.splice(r,1),r=this.ranges.indexOf(i),r!=-1&&this.ranges.splice(r,1),r=i.tabstop.rangeList.ranges.indexOf(i),r!=-1&&i.tabstop.splice(r,1),this.session.removeMarker(i.markerId),i.tabstop.length||(r=this.tabstops.indexOf(i.tabstop),r!=-1&&this.tabstops.splice(r,1),this.tabstops.length||this.detach())},t}();A.prototype.keyboardHandler=new d,A.prototype.keyboardHandler.bindKeys({Tab:function(t){l.snippetManager&&l.snippetManager.expandWithTab(t)||(t.tabstopManager.tabNext(1),t.renderer.scrollCursorIntoView())},"Shift-Tab":function(t){t.tabstopManager.tabNext(-1),t.renderer.scrollCursorIntoView()},Esc:function(t){t.tabstopManager.detach()}});var I=function(t,i){t.row==0&&(t.column+=i.column),t.row+=i.row},R=function(t,i){t.row==i.row&&(t.column-=i.column),t.row-=i.row};a.importCssString(`
.ace_snippet-marker {
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    background: rgba(194, 193, 208, 0.09);
    border: 1px dotted rgba(211, 208, 235, 0.62);
    position: absolute;
}`,"snippets.css",!1),l.snippetManager=new b;var S=n("./editor").Editor;(function(){this.insertSnippet=function(t,i){return l.snippetManager.insertSnippet(this,t,i)},this.expandSnippet=function(t){return l.snippetManager.expandWithTab(this,t)}}).call(S.prototype)}),ace.define("ace/ext/emmet",["require","exports","module","ace/keyboard/hash_handler","ace/editor","ace/snippets","ace/range","ace/config","resources","resources","tabStops","resources","utils","actions"],function(n,l,T){var e=n("../keyboard/hash_handler").HashHandler,a=n("../editor").Editor,u=n("../snippets").snippetManager,g=n("../range").Range,p=n("../config"),s,o,d=function(){function y(){}return y.prototype.setupContext=function(A){this.ace=A,this.indentation=A.session.getTabString(),s||(s=window.emmet);var I=s.resources||s.require("resources");I.setVariable("indentation",this.indentation),this.$syntax=null,this.$syntax=this.getSyntax()},y.prototype.getSelectionRange=function(){var A=this.ace.getSelectionRange(),I=this.ace.session.doc;return{start:I.positionToIndex(A.start),end:I.positionToIndex(A.end)}},y.prototype.createSelection=function(A,I){var R=this.ace.session.doc;this.ace.selection.setRange({start:R.indexToPosition(A),end:R.indexToPosition(I)})},y.prototype.getCurrentLineRange=function(){var A=this.ace,I=A.getCursorPosition().row,R=A.session.getLine(I).length,S=A.session.doc.positionToIndex({row:I,column:0});return{start:S,end:S+R}},y.prototype.getCaretPos=function(){var A=this.ace.getCursorPosition();return this.ace.session.doc.positionToIndex(A)},y.prototype.setCaretPos=function(A){var I=this.ace.session.doc.indexToPosition(A);this.ace.selection.moveToPosition(I)},y.prototype.getCurrentLine=function(){var A=this.ace.getCursorPosition().row;return this.ace.session.getLine(A)},y.prototype.replaceContent=function(A,I,R,S){R==null&&(R=I==null?this.getContent().length:I),I==null&&(I=0);var t=this.ace,i=t.session.doc,r=g.fromPoints(i.indexToPosition(I),i.indexToPosition(R));t.session.remove(r),r.end=r.start,A=this.$updateTabstops(A),u.insertSnippet(t,A)},y.prototype.getContent=function(){return this.ace.getValue()},y.prototype.getSyntax=function(){if(this.$syntax)return this.$syntax;var A=this.ace.session.$modeId.split("/").pop();if(A=="html"||A=="php"){var I=this.ace.getCursorPosition(),R=this.ace.session.getState(I.row);typeof R!="string"&&(R=R[0]),R&&(R=R.split("-"),R.length>1?A=R[0]:A=="php"&&(A="html"))}return A},y.prototype.getProfileName=function(){var A=s.resources||s.require("resources");switch(this.getSyntax()){case"css":return"css";case"xml":case"xsl":return"xml";case"html":var I=A.getVariable("profile");return I||(I=this.ace.session.getLines(0,2).join("").search(/<!DOCTYPE[^>]+XHTML/i)!=-1?"xhtml":"html"),I;default:var R=this.ace.session.$mode;return R.emmetConfig&&R.emmetConfig.profile||"xhtml"}},y.prototype.prompt=function(A){return prompt(A)},y.prototype.getSelection=function(){return this.ace.session.getTextRange()},y.prototype.getFilePath=function(){return""},y.prototype.$updateTabstops=function(A){var I=1e3,R=0,S=null,t=s.tabStops||s.require("tabStops"),i=s.resources||s.require("resources"),r=i.getVocabulary("user"),c={tabstop:function(x){var w=parseInt(x.group,10),$=w===0;$?w=++R:w+=I;var E=x.placeholder;E&&(E=t.processText(E,c));var M="${"+w+(E?":"+E:"")+"}";return $&&(S=[x.start,M]),M},escape:function(x){return x=="$"?"\\$":x=="\\"?"\\\\":x}};if(A=t.processText(A,c),r.variables.insert_final_tabstop&&!/\$\{0\}$/.test(A))A+="${0}";else if(S){var h=s.utils?s.utils.common:s.require("utils");A=h.replaceSubstring(A,"${0}",S[0],S[1])}return A},y}(),m={expand_abbreviation:{mac:"ctrl+alt+e",win:"alt+e"},match_pair_outward:{mac:"ctrl+d",win:"ctrl+,"},match_pair_inward:{mac:"ctrl+j",win:"ctrl+shift+0"},matching_pair:{mac:"ctrl+alt+j",win:"alt+j"},next_edit_point:"alt+right",prev_edit_point:"alt+left",toggle_comment:{mac:"command+/",win:"ctrl+/"},split_join_tag:{mac:"shift+command+'",win:"shift+ctrl+`"},remove_tag:{mac:"command+'",win:"shift+ctrl+;"},evaluate_math_expression:{mac:"shift+command+y",win:"shift+ctrl+y"},increment_number_by_1:"ctrl+up",decrement_number_by_1:"ctrl+down",increment_number_by_01:"alt+up",decrement_number_by_01:"alt+down",increment_number_by_10:{mac:"alt+command+up",win:"shift+alt+up"},decrement_number_by_10:{mac:"alt+command+down",win:"shift+alt+down"},select_next_item:{mac:"shift+command+.",win:"shift+ctrl+."},select_previous_item:{mac:"shift+command+,",win:"shift+ctrl+,"},reflect_css_value:{mac:"shift+command+r",win:"shift+ctrl+r"},encode_decode_data_url:{mac:"shift+ctrl+d",win:"ctrl+'"},expand_abbreviation_with_tab:"Tab",wrap_with_abbreviation:{mac:"shift+ctrl+a",win:"shift+ctrl+a"}},_=new d;l.commands=new e,l.runEmmetCommand=function y(A){if(this.action=="expand_abbreviation_with_tab"){if(!A.selection.isEmpty())return!1;var I=A.selection.lead,R=A.session.getTokenAt(I.row,I.column);if(R&&/\btag\b/.test(R.type))return!1}try{_.setupContext(A);var S=s.actions||s.require("actions");if(this.action=="wrap_with_abbreviation")return setTimeout(function(){S.run("wrap_with_abbreviation",_)},0);var t=S.run(this.action,_)}catch(r){if(!s){var i=l.load(y.bind(this,A));return this.action=="expand_abbreviation_with_tab"?!1:i}A._signal("changeStatus",typeof r=="string"?r:r.message),p.warn(r),t=!1}return t};for(var C in m)l.commands.addCommand({name:"emmet:"+C,action:C,bindKey:m[C],exec:l.runEmmetCommand,multiSelectAction:"forEach"});l.updateCommands=function(y,A){A?y.keyBinding.addKeyboardHandler(l.commands):y.keyBinding.removeKeyboardHandler(l.commands)},l.isSupportedMode=function(y){if(!y)return!1;if(y.emmetConfig)return!0;var A=y.$id||y;return/css|less|scss|sass|stylus|html|php|twig|ejs|handlebars/.test(A)},l.isAvailable=function(y,A){if(/(evaluate_math_expression|expand_abbreviation)$/.test(A))return!0;var I=y.session.$mode,R=l.isSupportedMode(I);if(R&&I.$modes)try{_.setupContext(y),/js|php/.test(_.getSyntax())&&(R=!1)}catch(S){}return R};var b=function(y,A){var I=A;if(I){var R=l.isSupportedMode(I.session.$mode);y.enableEmmet===!1&&(R=!1),R&&l.load(),l.updateCommands(I,R)}};l.load=function(y){return typeof o!="string"?(p.warn("script for emmet-core is not loaded"),!1):(p.loadModule(o,function(){o=null,y&&y()}),!0)},l.AceEmmetEditor=d,p.defineOptions(a.prototype,"editor",{enableEmmet:{set:function(y){this[y?"on":"removeListener"]("changeMode",b),b({enableEmmet:!!y},this)},value:!0}}),l.setCore=function(y){typeof y=="string"?o=y:s=y}}),function(){ace.require(["ace/ext/emmet"],function(n){k&&(k.exports=n)})}()})(pi);var hi={exports:{}};(function(k,f){ace.define("ace/snippets",["require","exports","module","ace/lib/dom","ace/lib/oop","ace/lib/event_emitter","ace/lib/lang","ace/range","ace/range_list","ace/keyboard/hash_handler","ace/tokenizer","ace/clipboard","ace/editor"],function(n,l,T){function e(t){var i=new Date().toLocaleString("en-us",t);return i.length==1?"0"+i:i}var a=n("./lib/dom"),u=n("./lib/oop"),g=n("./lib/event_emitter").EventEmitter,p=n("./lib/lang"),s=n("./range").Range,o=n("./range_list").RangeList,d=n("./keyboard/hash_handler").HashHandler,m=n("./tokenizer").Tokenizer,_=n("./clipboard"),C={CURRENT_WORD:function(t){return t.session.getTextRange(t.session.getWordRange())},SELECTION:function(t,i,r){var c=t.session.getTextRange();return r?c.replace(/\n\r?([ \t]*\S)/g,`
`+r+"$1"):c},CURRENT_LINE:function(t){return t.session.getLine(t.getCursorPosition().row)},PREV_LINE:function(t){return t.session.getLine(t.getCursorPosition().row-1)},LINE_INDEX:function(t){return t.getCursorPosition().row},LINE_NUMBER:function(t){return t.getCursorPosition().row+1},SOFT_TABS:function(t){return t.session.getUseSoftTabs()?"YES":"NO"},TAB_SIZE:function(t){return t.session.getTabSize()},CLIPBOARD:function(t){return _.getText&&_.getText()},FILENAME:function(t){return/[^/\\]*$/.exec(this.FILEPATH(t))[0]},FILENAME_BASE:function(t){return/[^/\\]*$/.exec(this.FILEPATH(t))[0].replace(/\.[^.]*$/,"")},DIRECTORY:function(t){return this.FILEPATH(t).replace(/[^/\\]*$/,"")},FILEPATH:function(t){return"/not implemented.txt"},WORKSPACE_NAME:function(){return"Unknown"},FULLNAME:function(){return"Unknown"},BLOCK_COMMENT_START:function(t){var i=t.session.$mode||{};return i.blockComment&&i.blockComment.start||""},BLOCK_COMMENT_END:function(t){var i=t.session.$mode||{};return i.blockComment&&i.blockComment.end||""},LINE_COMMENT:function(t){var i=t.session.$mode||{};return i.lineCommentStart||""},CURRENT_YEAR:e.bind(null,{year:"numeric"}),CURRENT_YEAR_SHORT:e.bind(null,{year:"2-digit"}),CURRENT_MONTH:e.bind(null,{month:"numeric"}),CURRENT_MONTH_NAME:e.bind(null,{month:"long"}),CURRENT_MONTH_NAME_SHORT:e.bind(null,{month:"short"}),CURRENT_DATE:e.bind(null,{day:"2-digit"}),CURRENT_DAY_NAME:e.bind(null,{weekday:"long"}),CURRENT_DAY_NAME_SHORT:e.bind(null,{weekday:"short"}),CURRENT_HOUR:e.bind(null,{hour:"2-digit",hour12:!1}),CURRENT_MINUTE:e.bind(null,{minute:"2-digit"}),CURRENT_SECOND:e.bind(null,{second:"2-digit"})};C.SELECTED_TEXT=C.SELECTION;var b=function(){function t(){this.snippetMap={},this.snippetNameMap={},this.variables=C}return t.prototype.getTokenizer=function(){return t.$tokenizer||this.createTokenizer()},t.prototype.createTokenizer=function(){function i(h){return h=h.substr(1),/^\d+$/.test(h)?[{tabstopId:parseInt(h,10)}]:[{text:h}]}function r(h){return"(?:[^\\\\"+h+"]|\\\\.)"}var c={regex:"/("+r("/")+"+)/",onMatch:function(h,x,w){var $=w[0];return $.fmtString=!0,$.guard=h.slice(1,-1),$.flag="",""},next:"formatString"};return t.$tokenizer=new m({start:[{regex:/\\./,onMatch:function(h,x,w){var $=h[1];return($=="}"&&w.length||"`$\\".indexOf($)!=-1)&&(h=$),[h]}},{regex:/}/,onMatch:function(h,x,w){return[w.length?w.shift():h]}},{regex:/\$(?:\d+|\w+)/,onMatch:i},{regex:/\$\{[\dA-Z_a-z]+/,onMatch:function(h,x,w){var $=i(h.substr(1));return w.unshift($[0]),$},next:"snippetVar"},{regex:/\n/,token:"newline",merge:!1}],snippetVar:[{regex:"\\|"+r("\\|")+"*\\|",onMatch:function(h,x,w){var $=h.slice(1,-1).replace(/\\[,|\\]|,/g,function(E){return E.length==2?E[1]:"\0"}).split("\0").map(function(E){return{value:E}});return w[0].choices=$,[$[0]]},next:"start"},c,{regex:"([^:}\\\\]|\\\\.)*:?",token:"",next:"start"}],formatString:[{regex:/:/,onMatch:function(h,x,w){return w.length&&w[0].expectElse?(w[0].expectElse=!1,w[0].ifEnd={elseEnd:w[0]},[w[0].ifEnd]):":"}},{regex:/\\./,onMatch:function(h,x,w){var $=h[1];return $=="}"&&w.length||"`$\\".indexOf($)!=-1?h=$:$=="n"?h=`
`:$=="t"?h="	":"ulULE".indexOf($)!=-1&&(h={changeCase:$,local:$>"a"}),[h]}},{regex:"/\\w*}",onMatch:function(h,x,w){var $=w.shift();return $&&($.flag=h.slice(1,-1)),this.next=$&&$.tabstopId?"start":"",[$||h]},next:"start"},{regex:/\$(?:\d+|\w+)/,onMatch:function(h,x,w){return[{text:h.slice(1)}]}},{regex:/\${\w+/,onMatch:function(h,x,w){var $={text:h.slice(2)};return w.unshift($),[$]},next:"formatStringVar"},{regex:/\n/,token:"newline",merge:!1},{regex:/}/,onMatch:function(h,x,w){var $=w.shift();return this.next=$&&$.tabstopId?"start":"",[$||h]},next:"start"}],formatStringVar:[{regex:/:\/\w+}/,onMatch:function(h,x,w){var $=w[0];return $.formatFunction=h.slice(2,-1),[w.shift()]},next:"formatString"},c,{regex:/:[\?\-+]?/,onMatch:function(h,x,w){h[1]=="+"&&(w[0].ifEnd=w[0]),h[1]=="?"&&(w[0].expectElse=!0)},next:"formatString"},{regex:"([^:}\\\\]|\\\\.)*:?",token:"",next:"formatString"}]}),t.$tokenizer},t.prototype.tokenizeTmSnippet=function(i,r){return this.getTokenizer().getLineTokens(i,r).tokens.map(function(c){return c.value||c})},t.prototype.getVariableValue=function(i,r,c){if(/^\d+$/.test(r))return(this.variables.__||{})[r]||"";if(/^[A-Z]\d+$/.test(r))return(this.variables[r[0]+"__"]||{})[r.substr(1)]||"";if(r=r.replace(/^TM_/,""),!this.variables.hasOwnProperty(r))return"";var h=this.variables[r];return typeof h=="function"&&(h=this.variables[r](i,r,c)),h==null?"":h},t.prototype.tmStrFormat=function(i,r,c){if(!r.fmt)return i;var h=r.flag||"",x=r.guard;x=new RegExp(x,h.replace(/[^gim]/g,""));var w=typeof r.fmt=="string"?this.tokenizeTmSnippet(r.fmt,"formatString"):r.fmt,$=this,E=i.replace(x,function(){var M=$.variables.__;$.variables.__=[].slice.call(arguments);for(var L=$.resolveVariables(w,c),N="E",P=0;P<L.length;P++){var z=L[P];if(typeof z=="object")if(L[P]="",z.changeCase&&z.local){var j=L[P+1];j&&typeof j=="string"&&(z.changeCase=="u"?L[P]=j[0].toUpperCase():L[P]=j[0].toLowerCase(),L[P+1]=j.substr(1))}else z.changeCase&&(N=z.changeCase);else N=="U"?L[P]=z.toUpperCase():N=="L"&&(L[P]=z.toLowerCase())}return $.variables.__=M,L.join("")});return E},t.prototype.tmFormatFunction=function(i,r,c){return r.formatFunction=="upcase"?i.toUpperCase():r.formatFunction=="downcase"?i.toLowerCase():i},t.prototype.resolveVariables=function(i,r){function c(N){var P=i.indexOf(N,$+1);P!=-1&&($=P)}for(var h=[],x="",w=!0,$=0;$<i.length;$++){var E=i[$];if(typeof E=="string"){h.push(E),E==`
`?(w=!0,x=""):w&&(x=/^\t*/.exec(E)[0],w=/\S/.test(E));continue}if(E){if(w=!1,E.fmtString){var M=i.indexOf(E,$+1);M==-1&&(M=i.length),E.fmt=i.slice($+1,M),$=M}if(E.text){var L=this.getVariableValue(r,E.text,x)+"";E.fmtString&&(L=this.tmStrFormat(L,E,r)),E.formatFunction&&(L=this.tmFormatFunction(L,E,r)),L&&!E.ifEnd?(h.push(L),c(E)):!L&&E.ifEnd&&c(E.ifEnd)}else E.elseEnd?c(E.elseEnd):(E.tabstopId!=null||E.changeCase!=null)&&h.push(E)}}return h},t.prototype.getDisplayTextForSnippet=function(i,r){var c=y.call(this,i,r);return c.text},t.prototype.insertSnippetForSelection=function(i,r,c){c===void 0&&(c={});var h=y.call(this,i,r,c),x=i.getSelectionRange(),w=i.session.replace(x,h.text),$=new A(i),E=i.inVirtualSelectionMode&&i.selection.index;$.addTabstops(h.tabstops,x.start,w,E)},t.prototype.insertSnippet=function(i,r,c){c===void 0&&(c={});var h=this;if(i.inVirtualSelectionMode)return h.insertSnippetForSelection(i,r,c);i.forEachSelection(function(){h.insertSnippetForSelection(i,r,c)},null,{keepOrder:!0}),i.tabstopManager&&i.tabstopManager.tabNext()},t.prototype.$getScope=function(i){var r=i.session.$mode.$id||"";if(r=r.split("/").pop(),r==="html"||r==="php"){r==="php"&&!i.session.$mode.inlinePhp&&(r="html");var c=i.getCursorPosition(),h=i.session.getState(c.row);typeof h=="object"&&(h=h[0]),h.substring&&(h.substring(0,3)=="js-"?r="javascript":h.substring(0,4)=="css-"?r="css":h.substring(0,4)=="php-"&&(r="php"))}return r},t.prototype.getActiveScopes=function(i){var r=this.$getScope(i),c=[r],h=this.snippetMap;return h[r]&&h[r].includeScopes&&c.push.apply(c,h[r].includeScopes),c.push("_"),c},t.prototype.expandWithTab=function(i,r){var c=this,h=i.forEachSelection(function(){return c.expandSnippetForSelection(i,r)},null,{keepOrder:!0});return h&&i.tabstopManager&&i.tabstopManager.tabNext(),h},t.prototype.expandSnippetForSelection=function(i,r){var c=i.getCursorPosition(),h=i.session.getLine(c.row),x=h.substring(0,c.column),w=h.substr(c.column),$=this.snippetMap,E;return this.getActiveScopes(i).some(function(M){var L=$[M];return L&&(E=this.findMatchingSnippet(L,x,w)),!!E},this),E?(r&&r.dryRun||(i.session.doc.removeInLine(c.row,c.column-E.replaceBefore.length,c.column+E.replaceAfter.length),this.variables.M__=E.matchBefore,this.variables.T__=E.matchAfter,this.insertSnippetForSelection(i,E.content),this.variables.M__=this.variables.T__=null),!0):!1},t.prototype.findMatchingSnippet=function(i,r,c){for(var h=i.length;h--;){var x=i[h];if(!(x.startRe&&!x.startRe.test(r))&&!(x.endRe&&!x.endRe.test(c))&&!(!x.startRe&&!x.endRe))return x.matchBefore=x.startRe?x.startRe.exec(r):[""],x.matchAfter=x.endRe?x.endRe.exec(c):[""],x.replaceBefore=x.triggerRe?x.triggerRe.exec(r)[0]:"",x.replaceAfter=x.endTriggerRe?x.endTriggerRe.exec(c)[0]:"",x}},t.prototype.register=function(i,r){function c(M){return M&&!/^\^?\(.*\)\$?$|^\\b$/.test(M)&&(M="(?:"+M+")"),M||""}function h(M,L,N){return M=c(M),L=c(L),N?(M=L+M,M&&M[M.length-1]!="$"&&(M+="$")):(M+=L,M&&M[0]!="^"&&(M="^"+M)),new RegExp(M)}function x(M){M.scope||(M.scope=r||"_"),r=M.scope,w[r]||(w[r]=[],$[r]={});var L=$[r];if(M.name){var N=L[M.name];N&&E.unregister(N),L[M.name]=M}w[r].push(M),M.prefix&&(M.tabTrigger=M.prefix),!M.content&&M.body&&(M.content=Array.isArray(M.body)?M.body.join(`
`):M.body),M.tabTrigger&&!M.trigger&&(!M.guard&&/^\w/.test(M.tabTrigger)&&(M.guard="\\b"),M.trigger=p.escapeRegExp(M.tabTrigger)),!(!M.trigger&&!M.guard&&!M.endTrigger&&!M.endGuard)&&(M.startRe=h(M.trigger,M.guard,!0),M.triggerRe=new RegExp(M.trigger),M.endRe=h(M.endTrigger,M.endGuard,!0),M.endTriggerRe=new RegExp(M.endTrigger))}var w=this.snippetMap,$=this.snippetNameMap,E=this;i||(i=[]),Array.isArray(i)?i.forEach(x):Object.keys(i).forEach(function(M){x(i[M])}),this._signal("registerSnippets",{scope:r})},t.prototype.unregister=function(i,r){function c(w){var $=x[w.scope||r];if($&&$[w.name]){delete $[w.name];var E=h[w.scope||r],M=E&&E.indexOf(w);M>=0&&E.splice(M,1)}}var h=this.snippetMap,x=this.snippetNameMap;i.content?c(i):Array.isArray(i)&&i.forEach(c)},t.prototype.parseSnippetFile=function(i){i=i.replace(/\r/g,"");for(var r=[],c={},h=/^#.*|^({[\s\S]*})\s*$|^(\S+) (.*)$|^((?:\n*\t.*)+)/gm,x;x=h.exec(i);){if(x[1])try{c=JSON.parse(x[1]),r.push(c)}catch(M){}if(x[4])c.content=x[4].replace(/^\t/gm,""),r.push(c),c={};else{var w=x[2],$=x[3];if(w=="regex"){var E=/\/((?:[^\/\\]|\\.)*)|$/g;c.guard=E.exec($)[1],c.trigger=E.exec($)[1],c.endTrigger=E.exec($)[1],c.endGuard=E.exec($)[1]}else w=="snippet"?(c.tabTrigger=$.match(/^\S*/)[0],c.name||(c.name=$)):w&&(c[w]=$)}}return r},t.prototype.getSnippetByName=function(i,r){var c=this.snippetNameMap,h;return this.getActiveScopes(r).some(function(x){var w=c[x];return w&&(h=w[i]),!!h},this),h},t}();u.implement(b.prototype,g);var y=function(t,i,r){function c(D){for(var X=[],J=0;J<D.length;J++){var U=D[J];if(typeof U=="object"){if(L[U.tabstopId])continue;var ue=D.lastIndexOf(U,J-1);U=X[ue]||{tabstopId:U.tabstopId}}X[J]=U}return X}r===void 0&&(r={});var h=t.getCursorPosition(),x=t.session.getLine(h.row),w=t.session.getTabString(),$=x.match(/^\s*/)[0];h.column<$.length&&($=$.slice(0,h.column)),i=i.replace(/\r/g,"");var E=this.tokenizeTmSnippet(i);E=this.resolveVariables(E,t),E=E.map(function(D){return D==`
`&&!r.excludeExtraIndent?D+$:typeof D=="string"?D.replace(/\t/g,w):D});var M=[];E.forEach(function(D,X){if(typeof D=="object"){var J=D.tabstopId,U=M[J];if(U||(U=M[J]=[],U.index=J,U.value="",U.parents={}),U.indexOf(D)===-1){D.choices&&!U.choices&&(U.choices=D.choices),U.push(D);var ue=E.indexOf(D,X+1);if(ue!==-1){var de=E.slice(X+1,ue),Ce=de.some(function(Fe){return typeof Fe=="object"});Ce&&!U.value?U.value=de:de.length&&(!U.value||typeof U.value!="string")&&(U.value=de.join(""))}}}}),M.forEach(function(D){D.length=0});for(var L={},N=0;N<E.length;N++){var P=E[N];if(typeof P=="object"){var z=P.tabstopId,j=M[z],V=E.indexOf(P,N+1);if(L[z]){L[z]===P&&(delete L[z],Object.keys(L).forEach(function(D){j.parents[D]=!0}));continue}L[z]=P;var ee=j.value;typeof ee!="string"?ee=c(ee):P.fmt&&(ee=this.tmStrFormat(ee,P,t)),E.splice.apply(E,[N+1,Math.max(0,V-N)].concat(ee,P)),j.indexOf(P)===-1&&j.push(P)}}var Z=0,ie=0,K="";return E.forEach(function(D){if(typeof D=="string"){var X=D.split(`
`);X.length>1?(ie=X[X.length-1].length,Z+=X.length-1):ie+=D.length,K+=D}else D&&(D.start?D.end={row:Z,column:ie}:D.start={row:Z,column:ie})}),{text:K,tabstops:M,tokens:E}},A=function(){function t(i){if(this.index=0,this.ranges=[],this.tabstops=[],i.tabstopManager)return i.tabstopManager;i.tabstopManager=this,this.$onChange=this.onChange.bind(this),this.$onChangeSelection=p.delayedCall(this.onChangeSelection.bind(this)).schedule,this.$onChangeSession=this.onChangeSession.bind(this),this.$onAfterExec=this.onAfterExec.bind(this),this.attach(i)}return t.prototype.attach=function(i){this.$openTabstops=null,this.selectedTabstop=null,this.editor=i,this.session=i.session,this.editor.on("change",this.$onChange),this.editor.on("changeSelection",this.$onChangeSelection),this.editor.on("changeSession",this.$onChangeSession),this.editor.commands.on("afterExec",this.$onAfterExec),this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler)},t.prototype.detach=function(){this.tabstops.forEach(this.removeTabstopMarkers,this),this.ranges.length=0,this.tabstops.length=0,this.selectedTabstop=null,this.editor.off("change",this.$onChange),this.editor.off("changeSelection",this.$onChangeSelection),this.editor.off("changeSession",this.$onChangeSession),this.editor.commands.off("afterExec",this.$onAfterExec),this.editor.keyBinding.removeKeyboardHandler(this.keyboardHandler),this.editor.tabstopManager=null,this.session=null,this.editor=null},t.prototype.onChange=function(i){for(var r=i.action[0]=="r",c=this.selectedTabstop||{},h=c.parents||{},x=this.tabstops.slice(),w=0;w<x.length;w++){var $=x[w],E=$==c||h[$.index];if($.rangeList.$bias=E?0:1,i.action=="remove"&&$!==c){var M=$.parents&&$.parents[c.index],L=$.rangeList.pointIndex(i.start,M);L=L<0?-L-1:L+1;var N=$.rangeList.pointIndex(i.end,M);N=N<0?-N-1:N-1;for(var P=$.rangeList.ranges.slice(L,N),z=0;z<P.length;z++)this.removeRange(P[z])}$.rangeList.$onChange(i)}var j=this.session;!this.$inChange&&r&&j.getLength()==1&&!j.getValue()&&this.detach()},t.prototype.updateLinkedFields=function(){var i=this.selectedTabstop;if(!(!i||!i.hasLinkedRanges||!i.firstNonLinked)){this.$inChange=!0;for(var r=this.session,c=r.getTextRange(i.firstNonLinked),h=0;h<i.length;h++){var x=i[h];if(x.linked){var w=x.original,$=l.snippetManager.tmStrFormat(c,w,this.editor);r.replace(x,$)}}this.$inChange=!1}},t.prototype.onAfterExec=function(i){i.command&&!i.command.readOnly&&this.updateLinkedFields()},t.prototype.onChangeSelection=function(){if(this.editor){for(var i=this.editor.selection.lead,r=this.editor.selection.anchor,c=this.editor.selection.isEmpty(),h=0;h<this.ranges.length;h++)if(!this.ranges[h].linked){var x=this.ranges[h].contains(i.row,i.column),w=c||this.ranges[h].contains(r.row,r.column);if(x&&w)return}this.detach()}},t.prototype.onChangeSession=function(){this.detach()},t.prototype.tabNext=function(i){var r=this.tabstops.length,c=this.index+(i||1);c=Math.min(Math.max(c,1),r),c==r&&(c=0),this.selectTabstop(c),this.updateTabstopMarkers(),c===0&&this.detach()},t.prototype.selectTabstop=function(i){this.$openTabstops=null;var r=this.tabstops[this.index];if(r&&this.addTabstopMarkers(r),this.index=i,r=this.tabstops[this.index],!(!r||!r.length)){this.selectedTabstop=r;var c=r.firstNonLinked||r;if(r.choices&&(c.cursor=c.start),this.editor.inVirtualSelectionMode)this.editor.selection.fromOrientedRange(c);else{var h=this.editor.multiSelect;h.toSingleRange(c);for(var x=0;x<r.length;x++)r.hasLinkedRanges&&r[x].linked||h.addRange(r[x].clone(),!0)}this.editor.keyBinding.addKeyboardHandler(this.keyboardHandler),this.selectedTabstop&&this.selectedTabstop.choices&&this.editor.execCommand("startAutocomplete",{matches:this.selectedTabstop.choices})}},t.prototype.addTabstops=function(i,r,c){var h=this.useLink||!this.editor.getOption("enableMultiselect");if(this.$openTabstops||(this.$openTabstops=[]),!i[0]){var x=s.fromPoints(c,c);R(x.start,r),R(x.end,r),i[0]=[x],i[0].index=0}var w=this.index,$=[w+1,0],E=this.ranges,M=this.snippetId=(this.snippetId||0)+1;i.forEach(function(L,N){var P=this.$openTabstops[N]||L;P.snippetId=M;for(var z=0;z<L.length;z++){var j=L[z],V=s.fromPoints(j.start,j.end||j.start);I(V.start,r),I(V.end,r),V.original=j,V.tabstop=P,E.push(V),P!=L?P.unshift(V):P[z]=V,j.fmtString||P.firstNonLinked&&h?(V.linked=!0,P.hasLinkedRanges=!0):P.firstNonLinked||(P.firstNonLinked=V)}P.firstNonLinked||(P.hasLinkedRanges=!1),P===L&&($.push(P),this.$openTabstops[N]=P),this.addTabstopMarkers(P),P.rangeList=P.rangeList||new o,P.rangeList.$bias=0,P.rangeList.addList(P)},this),$.length>2&&(this.tabstops.length&&$.push($.splice(2,1)[0]),this.tabstops.splice.apply(this.tabstops,$))},t.prototype.addTabstopMarkers=function(i){var r=this.session;i.forEach(function(c){c.markerId||(c.markerId=r.addMarker(c,"ace_snippet-marker","text"))})},t.prototype.removeTabstopMarkers=function(i){var r=this.session;i.forEach(function(c){r.removeMarker(c.markerId),c.markerId=null})},t.prototype.updateTabstopMarkers=function(){if(this.selectedTabstop){var i=this.selectedTabstop.snippetId;this.selectedTabstop.index===0&&i--,this.tabstops.forEach(function(r){r.snippetId===i?this.addTabstopMarkers(r):this.removeTabstopMarkers(r)},this)}},t.prototype.removeRange=function(i){var r=i.tabstop.indexOf(i);r!=-1&&i.tabstop.splice(r,1),r=this.ranges.indexOf(i),r!=-1&&this.ranges.splice(r,1),r=i.tabstop.rangeList.ranges.indexOf(i),r!=-1&&i.tabstop.splice(r,1),this.session.removeMarker(i.markerId),i.tabstop.length||(r=this.tabstops.indexOf(i.tabstop),r!=-1&&this.tabstops.splice(r,1),this.tabstops.length||this.detach())},t}();A.prototype.keyboardHandler=new d,A.prototype.keyboardHandler.bindKeys({Tab:function(t){l.snippetManager&&l.snippetManager.expandWithTab(t)||(t.tabstopManager.tabNext(1),t.renderer.scrollCursorIntoView())},"Shift-Tab":function(t){t.tabstopManager.tabNext(-1),t.renderer.scrollCursorIntoView()},Esc:function(t){t.tabstopManager.detach()}});var I=function(t,i){t.row==0&&(t.column+=i.column),t.row+=i.row},R=function(t,i){t.row==i.row&&(t.column-=i.column),t.row-=i.row};a.importCssString(`
.ace_snippet-marker {
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    background: rgba(194, 193, 208, 0.09);
    border: 1px dotted rgba(211, 208, 235, 0.62);
    position: absolute;
}`,"snippets.css",!1),l.snippetManager=new b;var S=n("./editor").Editor;(function(){this.insertSnippet=function(t,i){return l.snippetManager.insertSnippet(this,t,i)},this.expandSnippet=function(t){return l.snippetManager.expandWithTab(this,t)}}).call(S.prototype)}),ace.define("ace/autocomplete/popup",["require","exports","module","ace/virtual_renderer","ace/editor","ace/range","ace/lib/event","ace/lib/lang","ace/lib/dom","ace/config","ace/lib/useragent"],function(n,l,T){var e=n("../virtual_renderer").VirtualRenderer,a=n("../editor").Editor,u=n("../range").Range,g=n("../lib/event"),p=n("../lib/lang"),s=n("../lib/dom"),o=n("../config").nls,d=n("./../lib/useragent"),m=function(I){return"suggest-aria-id:".concat(I)},_=d.isSafari?"menu":"listbox",C=d.isSafari?"menuitem":"option",b=d.isSafari?"aria-current":"aria-selected",y=function(I){var R=new e(I);R.$maxLines=4;var S=new a(R);return S.setHighlightActiveLine(!1),S.setShowPrintMargin(!1),S.renderer.setShowGutter(!1),S.renderer.setHighlightGutterLine(!1),S.$mouseHandler.$focusTimeout=0,S.$highlightTagPending=!0,S},A=function(){function I(R){var S=s.createElement("div"),t=y(S);R&&R.appendChild(S),S.style.display="none",t.renderer.content.style.cursor="default",t.renderer.setStyle("ace_autocomplete"),t.renderer.$textLayer.element.setAttribute("role",_),t.renderer.$textLayer.element.setAttribute("aria-roledescription",o("autocomplete.popup.aria-roledescription","Autocomplete suggestions")),t.renderer.$textLayer.element.setAttribute("aria-label",o("autocomplete.popup.aria-label","Autocomplete suggestions")),t.renderer.textarea.setAttribute("aria-hidden","true"),t.setOption("displayIndentGuides",!1),t.setOption("dragDelay",150);var i=function(){};t.focus=i,t.$isFocused=!0,t.renderer.$cursorLayer.restartTimer=i,t.renderer.$cursorLayer.element.style.opacity="0",t.renderer.$maxLines=8,t.renderer.$keepTextAreaAtCursor=!1,t.setHighlightActiveLine(!1),t.session.highlight(""),t.session.$searchHighlight.clazz="ace_highlight-marker",t.on("mousedown",function(E){var M=E.getDocumentPosition();t.selection.moveToPosition(M),h.start.row=h.end.row=M.row,E.stop()});var r,c=new u(-1,0,-1,1/0),h=new u(-1,0,-1,1/0);h.id=t.session.addMarker(h,"ace_active-line","fullLine"),t.setSelectOnHover=function(E){E?c.id&&(t.session.removeMarker(c.id),c.id=null):c.id=t.session.addMarker(c,"ace_line-hover","fullLine")},t.setSelectOnHover(!1),t.on("mousemove",function(E){if(!r){r=E;return}if(!(r.x==E.x&&r.y==E.y)){r=E,r.scrollTop=t.renderer.scrollTop,t.isMouseOver=!0;var M=r.getDocumentPosition().row;c.start.row!=M&&(c.id||t.setRow(M),w(M))}}),t.renderer.on("beforeRender",function(){if(r&&c.start.row!=-1){r.$pos=null;var E=r.getDocumentPosition().row;c.id||t.setRow(E),w(E,!0)}}),t.renderer.on("afterRender",function(){var E=t.getRow(),M=t.renderer.$textLayer,L=M.element.childNodes[E-M.config.firstRow],N=document.activeElement;if(L!==t.selectedNode&&t.selectedNode&&(s.removeCssClass(t.selectedNode,"ace_selected"),N.removeAttribute("aria-activedescendant"),t.selectedNode.removeAttribute(b),t.selectedNode.removeAttribute("id")),t.selectedNode=L,L){s.addCssClass(L,"ace_selected");var P=m(E);L.id=P,M.element.setAttribute("aria-activedescendant",P),N.setAttribute("aria-activedescendant",P),L.setAttribute("role",C),L.setAttribute("aria-roledescription",o("autocomplete.popup.item.aria-roledescription","item")),L.setAttribute("aria-label",t.getData(E).caption||t.getData(E).value),L.setAttribute("aria-setsize",t.data.length),L.setAttribute("aria-posinset",E+1),L.setAttribute("aria-describedby","doc-tooltip"),L.setAttribute(b,"true")}});var x=function(){w(-1)},w=function(E,M){E!==c.start.row&&(c.start.row=c.end.row=E,M||t.session._emit("changeBackMarker"),t._emit("changeHoverMarker"))};t.getHoveredRow=function(){return c.start.row},g.addListener(t.container,"mouseout",function(){t.isMouseOver=!1,x()}),t.on("hide",x),t.on("changeSelection",x),t.session.doc.getLength=function(){return t.data.length},t.session.doc.getLine=function(E){var M=t.data[E];return typeof M=="string"?M:M&&M.value||""};var $=t.session.bgTokenizer;return $.$tokenizeRow=function(E){function M(D,X){D&&N.push({type:(L.className||"")+(X||""),value:D})}var L=t.data[E],N=[];if(!L)return N;typeof L=="string"&&(L={value:L});for(var P=L.caption||L.value||L.name,z=P.toLowerCase(),j=(t.filterText||"").toLowerCase(),V=0,ee=0,Z=0;Z<=j.length;Z++)if(Z!=ee&&(L.matchMask&1<<Z||Z==j.length)){var ie=j.slice(ee,Z);ee=Z;var K=z.indexOf(ie,V);if(K==-1)continue;M(P.slice(V,K),""),V=K+ie.length,M(P.slice(K,V),"completion-highlight")}return M(P.slice(V,P.length),""),N.push({type:"completion-spacer",value:" "}),L.meta&&N.push({type:"completion-meta",value:L.meta}),L.message&&N.push({type:"completion-message",value:L.message}),N},$.$updateOnChange=i,$.start=i,t.session.$computeWidth=function(){return this.screenWidth=0},t.isOpen=!1,t.isTopdown=!1,t.autoSelect=!0,t.filterText="",t.isMouseOver=!1,t.data=[],t.setData=function(E,M){t.filterText=M||"",t.setValue(p.stringRepeat(`
`,E.length),-1),t.data=E||[],t.setRow(0)},t.getData=function(E){return t.data[E]},t.getRow=function(){return h.start.row},t.setRow=function(E){E=Math.max(this.autoSelect?0:-1,Math.min(this.data.length-1,E)),h.start.row!=E&&(t.selection.clearSelection(),h.start.row=h.end.row=E||0,t.session._emit("changeBackMarker"),t.moveCursorTo(E||0,0),t.isOpen&&t._signal("select"))},t.on("changeSelection",function(){t.isOpen&&t.setRow(t.selection.lead.row),t.renderer.scrollCursorIntoView()}),t.hide=function(){this.container.style.display="none",t.anchorPos=null,t.anchor=null,t.isOpen&&(t.isOpen=!1,this._signal("hide"))},t.tryShow=function(E,M,L,N){if(!N&&t.isOpen&&t.anchorPos&&t.anchor&&t.anchorPos.top===E.top&&t.anchorPos.left===E.left&&t.anchor===L)return!0;var P=this.container,z=window.innerHeight,j=window.innerWidth,V=this.renderer,ee=V.$maxLines*M*1.4,Z={top:0,bottom:0,left:0},ie=z-E.top-3*this.$borderSize-M,K=E.top-3*this.$borderSize;L||(K<=ie||ie>=ee?L="bottom":L="top"),L==="top"?(Z.bottom=E.top-this.$borderSize,Z.top=Z.bottom-ee):L==="bottom"&&(Z.top=E.top+M+this.$borderSize,Z.bottom=Z.top+ee);var D=Z.top>=0&&Z.bottom<=z;if(!N&&!D)return!1;D?V.$maxPixelHeight=null:L==="top"?V.$maxPixelHeight=K:V.$maxPixelHeight=ie,L==="top"?(P.style.top="",P.style.bottom=z-Z.bottom+"px",t.isTopdown=!1):(P.style.top=Z.top+"px",P.style.bottom="",t.isTopdown=!0),P.style.display="";var X=E.left;return X+P.offsetWidth>j&&(X=j-P.offsetWidth),P.style.left=X+"px",P.style.right="",t.isOpen||(t.isOpen=!0,this._signal("show"),r=null),t.anchorPos=E,t.anchor=L,!0},t.show=function(E,M,L){this.tryShow(E,M,L?"bottom":void 0,!0)},t.goTo=function(E){var M=this.getRow(),L=this.session.getLength()-1;switch(E){case"up":M=M<=0?L:M-1;break;case"down":M=M>=L?-1:M+1;break;case"start":M=0;break;case"end":M=L}this.setRow(M)},t.getTextLeftOffset=function(){return this.$borderSize+this.renderer.$padding+this.$imageSize},t.$imageSize=0,t.$borderSize=1,t}return I}();s.importCssString(`
.ace_editor.ace_autocomplete .ace_marker-layer .ace_active-line {
    background-color: #CAD6FA;
    z-index: 1;
}
.ace_dark.ace_editor.ace_autocomplete .ace_marker-layer .ace_active-line {
    background-color: #3a674e;
}
.ace_editor.ace_autocomplete .ace_line-hover {
    border: 1px solid #abbffe;
    margin-top: -1px;
    background: rgba(233,233,253,0.4);
    position: absolute;
    z-index: 2;
}
.ace_dark.ace_editor.ace_autocomplete .ace_line-hover {
    border: 1px solid rgba(109, 150, 13, 0.8);
    background: rgba(58, 103, 78, 0.62);
}
.ace_completion-meta {
    opacity: 0.5;
    margin-left: 0.9em;
}
.ace_completion-message {
    margin-left: 0.9em;
    color: blue;
}
.ace_editor.ace_autocomplete .ace_completion-highlight{
    color: #2d69c7;
}
.ace_dark.ace_editor.ace_autocomplete .ace_completion-highlight{
    color: #93ca12;
}
.ace_editor.ace_autocomplete {
    width: 300px;
    z-index: 200000;
    border: 1px lightgray solid;
    position: fixed;
    box-shadow: 2px 3px 5px rgba(0,0,0,.2);
    line-height: 1.4;
    background: #fefefe;
    color: #111;
}
.ace_dark.ace_editor.ace_autocomplete {
    border: 1px #484747 solid;
    box-shadow: 2px 3px 5px rgba(0, 0, 0, 0.51);
    line-height: 1.4;
    background: #25282c;
    color: #c1c1c1;
}
.ace_autocomplete .ace_text-layer  {
    width: calc(100% - 8px);
}
.ace_autocomplete .ace_line {
    display: flex;
    align-items: center;
}
.ace_autocomplete .ace_line > * {
    min-width: 0;
    flex: 0 0 auto;
}
.ace_autocomplete .ace_line .ace_ {
    flex: 0 1 auto;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ace_autocomplete .ace_completion-spacer {
    flex: 1;
}
.ace_autocomplete.ace_loading:after  {
    content: "";
    position: absolute;
    top: 0px;
    height: 2px;
    width: 8%;
    background: blue;
    z-index: 100;
    animation: ace_progress 3s infinite linear;
    animation-delay: 300ms;
    transform: translateX(-100%) scaleX(1);
}
@keyframes ace_progress {
    0% { transform: translateX(-100%) scaleX(1) }
    50% { transform: translateX(625%) scaleX(2) } 
    100% { transform: translateX(1500%) scaleX(3) } 
}
@media (prefers-reduced-motion) {
    .ace_autocomplete.ace_loading:after {
        transform: translateX(625%) scaleX(2);
        animation: none;
     }
}
`,"autocompletion.css",!1),l.AcePopup=A,l.$singleLineEditor=y,l.getAriaId=m}),ace.define("ace/autocomplete/inline_screenreader",["require","exports","module"],function(n,l,T){var e=function(){function a(u){this.editor=u,this.screenReaderDiv=document.createElement("div"),this.screenReaderDiv.classList.add("ace_screenreader-only"),this.editor.container.appendChild(this.screenReaderDiv)}return a.prototype.setScreenReaderContent=function(u){for(!this.popup&&this.editor.completer&&this.editor.completer.popup&&(this.popup=this.editor.completer.popup,this.popup.renderer.on("afterRender",function(){var p=this.popup.getRow(),s=this.popup.renderer.$textLayer,o=s.element.childNodes[p-s.config.firstRow];if(o){for(var d="doc-tooltip ",m=0;m<this._lines.length;m++)d+="ace-inline-screenreader-line-".concat(m," ");o.setAttribute("aria-describedby",d)}}.bind(this)));this.screenReaderDiv.firstChild;)this.screenReaderDiv.removeChild(this.screenReaderDiv.firstChild);this._lines=u.split(/\r\n|\r|\n/);var g=this.createCodeBlock();this.screenReaderDiv.appendChild(g)},a.prototype.destroy=function(){this.screenReaderDiv.remove()},a.prototype.createCodeBlock=function(){var u=document.createElement("pre");u.setAttribute("id","ace-inline-screenreader");for(var g=0;g<this._lines.length;g++){var p=document.createElement("code");p.setAttribute("id","ace-inline-screenreader-line-".concat(g));var s=document.createTextNode(this._lines[g]);p.appendChild(s),u.appendChild(p)}return u},a}();l.AceInlineScreenReader=e}),ace.define("ace/autocomplete/inline",["require","exports","module","ace/snippets","ace/autocomplete/inline_screenreader"],function(n,l,T){var e=n("../snippets").snippetManager,a=n("./inline_screenreader").AceInlineScreenReader,u=function(){function g(){this.editor=null}return g.prototype.show=function(p,s,o){if(o=o||"",p&&this.editor&&this.editor!==p&&(this.hide(),this.editor=null,this.inlineScreenReader=null),!p||!s)return!1;this.inlineScreenReader||(this.inlineScreenReader=new a(p));var d=s.snippet?e.getDisplayTextForSnippet(p,s.snippet):s.value;return s.hideInlinePreview||!d||!d.startsWith(o)?!1:(this.editor=p,this.inlineScreenReader.setScreenReaderContent(d),d=d.slice(o.length),d===""?p.removeGhostText():p.setGhostText(d),!0)},g.prototype.isOpen=function(){return this.editor?!!this.editor.renderer.$ghostText:!1},g.prototype.hide=function(){return this.editor?(this.editor.removeGhostText(),!0):!1},g.prototype.destroy=function(){this.hide(),this.editor=null,this.inlineScreenReader&&(this.inlineScreenReader.destroy(),this.inlineScreenReader=null)},g}();l.AceInline=u}),ace.define("ace/autocomplete/util",["require","exports","module"],function(n,l,T){l.parForEach=function(a,u,g){var p=0,s=a.length;s===0&&g();for(var o=0;o<s;o++)u(a[o],function(d,m){p++,p===s&&g(d,m)})};var e=/[a-zA-Z_0-9\$\-\u00A2-\u2000\u2070-\uFFFF]/;l.retrievePrecedingIdentifier=function(a,u,g){g=g||e;for(var p=[],s=u-1;s>=0&&g.test(a[s]);s--)p.push(a[s]);return p.reverse().join("")},l.retrieveFollowingIdentifier=function(a,u,g){g=g||e;for(var p=[],s=u;s<a.length&&g.test(a[s]);s++)p.push(a[s]);return p},l.getCompletionPrefix=function(a){var u=a.getCursorPosition(),g=a.session.getLine(u.row),p;return a.completers.forEach(function(s){s.identifierRegexps&&s.identifierRegexps.forEach(function(o){!p&&o&&(p=this.retrievePrecedingIdentifier(g,u.column,o))}.bind(this))}.bind(this)),p||this.retrievePrecedingIdentifier(g,u.column)},l.triggerAutocomplete=function(a,g){var g=g==null?a.session.getPrecedingCharacter():g;return a.completers.some(function(p){if(p.triggerCharacters&&Array.isArray(p.triggerCharacters))return p.triggerCharacters.includes(g)})}}),ace.define("ace/autocomplete",["require","exports","module","ace/keyboard/hash_handler","ace/autocomplete/popup","ace/autocomplete/inline","ace/autocomplete/popup","ace/autocomplete/util","ace/lib/lang","ace/lib/dom","ace/snippets","ace/config","ace/lib/event","ace/lib/scroll"],function(n,l,T){var e=n("./keyboard/hash_handler").HashHandler,a=n("./autocomplete/popup").AcePopup,u=n("./autocomplete/inline").AceInline,g=n("./autocomplete/popup").getAriaId,p=n("./autocomplete/util"),s=n("./lib/lang"),o=n("./lib/dom"),d=n("./snippets").snippetManager,m=n("./config"),_=n("./lib/event"),C=n("./lib/scroll").preventParentScroll,b=function(R,S){S.completer&&S.completer.destroy()},y=function(){function R(){this.autoInsert=!1,this.autoSelect=!0,this.autoShown=!1,this.exactMatch=!1,this.inlineEnabled=!1,this.keyboardHandler=new e,this.keyboardHandler.bindKeys(this.commands),this.parentNode=null,this.setSelectOnHover=!1,this.hasSeen=new Set,this.showLoadingState=!1,this.stickySelectionDelay=500,this.blurListener=this.blurListener.bind(this),this.changeListener=this.changeListener.bind(this),this.mousedownListener=this.mousedownListener.bind(this),this.mousewheelListener=this.mousewheelListener.bind(this),this.onLayoutChange=this.onLayoutChange.bind(this),this.changeTimer=s.delayedCall(function(){this.updateCompletions(!0)}.bind(this)),this.tooltipTimer=s.delayedCall(this.updateDocTooltip.bind(this),50),this.popupTimer=s.delayedCall(this.$updatePopupPosition.bind(this),50),this.stickySelectionTimer=s.delayedCall(function(){this.stickySelection=!0}.bind(this),this.stickySelectionDelay),this.$firstOpenTimer=s.delayedCall(function(){var S=this.completionProvider&&this.completionProvider.initialPosition;this.autoShown||this.popup&&this.popup.isOpen||!S||this.editor.completers.length===0||(this.completions=new I(R.completionsForLoading),this.openPopup(this.editor,S.prefix,!1),this.popup.renderer.setStyle("ace_loading",!0))}.bind(this),this.stickySelectionDelay)}return Object.defineProperty(R,"completionsForLoading",{get:function(){return[{caption:m.nls("autocomplete.loading","Loading..."),value:""}]},enumerable:!1,configurable:!0}),R.prototype.$init=function(){return this.popup=new a(this.parentNode||document.body||document.documentElement),this.popup.on("click",function(S){this.insertMatch(),S.stop()}.bind(this)),this.popup.focus=this.editor.focus.bind(this.editor),this.popup.on("show",this.$onPopupShow.bind(this)),this.popup.on("hide",this.$onHidePopup.bind(this)),this.popup.on("select",this.$onPopupChange.bind(this)),_.addListener(this.popup.container,"mouseout",this.mouseOutListener.bind(this)),this.popup.on("changeHoverMarker",this.tooltipTimer.bind(null,null)),this.popup.renderer.on("afterRender",this.$onPopupRender.bind(this)),this.popup},R.prototype.$initInline=function(){if(!(!this.inlineEnabled||this.inlineRenderer))return this.inlineRenderer=new u,this.inlineRenderer},R.prototype.getPopup=function(){return this.popup||this.$init()},R.prototype.$onHidePopup=function(){this.inlineRenderer&&this.inlineRenderer.hide(),this.hideDocTooltip(),this.stickySelectionTimer.cancel(),this.popupTimer.cancel(),this.stickySelection=!1},R.prototype.$seen=function(S){!this.hasSeen.has(S)&&S&&S.completer&&S.completer.onSeen&&typeof S.completer.onSeen=="function"&&(S.completer.onSeen(this.editor,S),this.hasSeen.add(S))},R.prototype.$onPopupChange=function(S){if(this.inlineRenderer&&this.inlineEnabled){var t=S?null:this.popup.getData(this.popup.getRow());if(this.$updateGhostText(t),this.popup.isMouseOver&&this.setSelectOnHover){this.tooltipTimer.call(null,null);return}this.popupTimer.schedule(),this.tooltipTimer.schedule()}else this.popupTimer.call(null,null),this.tooltipTimer.call(null,null)},R.prototype.$updateGhostText=function(S){var t=this.base.row,i=this.base.column,r=this.editor.getCursorPosition().column,c=this.editor.session.getLine(t).slice(i,r);this.inlineRenderer.show(this.editor,S,c)?this.$seen(S):this.inlineRenderer.hide()},R.prototype.$onPopupRender=function(){var S=this.inlineRenderer&&this.inlineEnabled;if(this.completions&&this.completions.filtered&&this.completions.filtered.length>0)for(var t=this.popup.getFirstVisibleRow();t<=this.popup.getLastVisibleRow();t++){var i=this.popup.getData(t);i&&(!S||i.hideInlinePreview)&&this.$seen(i)}},R.prototype.$onPopupShow=function(S){this.$onPopupChange(S),this.stickySelection=!1,this.stickySelectionDelay>=0&&this.stickySelectionTimer.schedule(this.stickySelectionDelay)},R.prototype.observeLayoutChanges=function(){if(!(this.$elements||!this.editor)){window.addEventListener("resize",this.onLayoutChange,{passive:!0}),window.addEventListener("wheel",this.mousewheelListener);for(var S=this.editor.container.parentNode,t=[];S;)t.push(S),S.addEventListener("scroll",this.onLayoutChange,{passive:!0}),S=S.parentNode;this.$elements=t}},R.prototype.unObserveLayoutChanges=function(){var S=this;window.removeEventListener("resize",this.onLayoutChange,{passive:!0}),window.removeEventListener("wheel",this.mousewheelListener),this.$elements&&this.$elements.forEach(function(t){t.removeEventListener("scroll",S.onLayoutChange,{passive:!0})}),this.$elements=null},R.prototype.onLayoutChange=function(){if(!this.popup.isOpen)return this.unObserveLayoutChanges();this.$updatePopupPosition(),this.updateDocTooltip()},R.prototype.$updatePopupPosition=function(){var S=this.editor,t=S.renderer,i=t.layerConfig.lineHeight,r=t.$cursorLayer.getPixelPosition(this.base,!0);r.left-=this.popup.getTextLeftOffset();var c=S.container.getBoundingClientRect();r.top+=c.top-t.layerConfig.offset,r.left+=c.left-S.renderer.scrollLeft,r.left+=t.gutterWidth;var h={top:r.top,left:r.left};t.$ghostText&&t.$ghostTextWidget&&this.base.row===t.$ghostText.position.row&&(h.top+=t.$ghostTextWidget.el.offsetHeight);var x=S.container.getBoundingClientRect().bottom-i,w=x<h.top?{top:x,left:h.left}:h;this.popup.tryShow(w,i,"bottom")||this.popup.tryShow(r,i,"top")||this.popup.show(r,i)},R.prototype.openPopup=function(S,t,i){this.$firstOpenTimer.cancel(),this.popup||this.$init(),this.inlineEnabled&&!this.inlineRenderer&&this.$initInline(),this.popup.autoSelect=this.autoSelect,this.popup.setSelectOnHover(this.setSelectOnHover);var r=this.popup.getRow(),c=this.popup.data[r];this.popup.setData(this.completions.filtered,this.completions.filterText),this.editor.textInput.setAriaOptions&&this.editor.textInput.setAriaOptions({activeDescendant:g(this.popup.getRow()),inline:this.inlineEnabled}),S.keyBinding.addKeyboardHandler(this.keyboardHandler);var h;this.stickySelection&&(h=this.popup.data.indexOf(c)),(!h||h===-1)&&(h=0),this.popup.setRow(this.autoSelect?h:-1),h===r&&c!==this.completions.filtered[h]&&this.$onPopupChange();var x=this.inlineRenderer&&this.inlineEnabled;if(h===r&&x){var w=this.popup.getData(this.popup.getRow());this.$updateGhostText(w)}i||(this.popup.setTheme(S.getTheme()),this.popup.setFontSize(S.getFontSize()),this.$updatePopupPosition(),this.tooltipNode&&this.updateDocTooltip()),this.changeTimer.cancel(),this.observeLayoutChanges()},R.prototype.detach=function(){this.editor&&(this.editor.keyBinding.removeKeyboardHandler(this.keyboardHandler),this.editor.off("changeSelection",this.changeListener),this.editor.off("blur",this.blurListener),this.editor.off("mousedown",this.mousedownListener),this.editor.off("mousewheel",this.mousewheelListener)),this.$firstOpenTimer.cancel(),this.changeTimer.cancel(),this.hideDocTooltip(),this.completionProvider&&this.completionProvider.detach(),this.popup&&this.popup.isOpen&&this.popup.hide(),this.popup&&this.popup.renderer&&this.popup.renderer.off("afterRender",this.$onPopupRender),this.base&&this.base.detach(),this.activated=!1,this.completionProvider=this.completions=this.base=null,this.unObserveLayoutChanges()},R.prototype.changeListener=function(S){var t=this.editor.selection.lead;(t.row!=this.base.row||t.column<this.base.column)&&this.detach(),this.activated?this.changeTimer.schedule():this.detach()},R.prototype.blurListener=function(S){var t=document.activeElement,i=this.editor.textInput.getElement(),r=S.relatedTarget&&this.tooltipNode&&this.tooltipNode.contains(S.relatedTarget),c=this.popup&&this.popup.container;t!=i&&t.parentNode!=c&&!r&&t!=this.tooltipNode&&S.relatedTarget!=i&&this.detach()},R.prototype.mousedownListener=function(S){this.detach()},R.prototype.mousewheelListener=function(S){this.popup&&!this.popup.isMouseOver&&this.detach()},R.prototype.mouseOutListener=function(S){this.popup.isOpen&&this.$updatePopupPosition()},R.prototype.goTo=function(S){this.popup.goTo(S)},R.prototype.insertMatch=function(S,t){if(S||(S=this.popup.getData(this.popup.getRow())),!S)return!1;if(S.value==="")return this.detach();var i=this.completions,r=this.getCompletionProvider().insertMatch(this.editor,S,i.filterText,t);return this.completions==i&&this.detach(),r},R.prototype.showPopup=function(S,t){this.editor&&this.detach(),this.activated=!0,this.editor=S,S.completer!=this&&(S.completer&&S.completer.detach(),S.completer=this),S.on("changeSelection",this.changeListener),S.on("blur",this.blurListener),S.on("mousedown",this.mousedownListener),S.on("mousewheel",this.mousewheelListener),this.updateCompletions(!1,t)},R.prototype.getCompletionProvider=function(S){return this.completionProvider||(this.completionProvider=new A(S)),this.completionProvider},R.prototype.gatherCompletions=function(S,t){return this.getCompletionProvider().gatherCompletions(S,t)},R.prototype.updateCompletions=function(S,t){if(S&&this.base&&this.completions){var r=this.editor.getCursorPosition(),c=this.editor.session.getTextRange({start:this.base,end:r});if(c==this.completions.filterText)return;if(this.completions.setFilter(c),!this.completions.filtered.length)return this.detach();if(this.completions.filtered.length==1&&this.completions.filtered[0].value==c&&!this.completions.filtered[0].snippet)return this.detach();this.openPopup(this.editor,c,S);return}if(t&&t.matches){var r=this.editor.getSelectionRange().start;return this.base=this.editor.session.doc.createAnchor(r.row,r.column),this.base.$insertRight=!0,this.completions=new I(t.matches),this.getCompletionProvider().completions=this.completions,this.openPopup(this.editor,"",S)}var i=this.editor.getSession(),r=this.editor.getCursorPosition(),c=p.getCompletionPrefix(this.editor);this.base=i.doc.createAnchor(r.row,r.column-c.length),this.base.$insertRight=!0;var h={exactMatch:this.exactMatch,ignoreCaption:this.ignoreCaption};this.getCompletionProvider({prefix:c,pos:r}).provideCompletions(this.editor,h,function(x,w,$){var E=w.filtered,M=p.getCompletionPrefix(this.editor);if(this.$firstOpenTimer.cancel(),$){if(!E.length){var L=!this.autoShown&&this.emptyMessage;if(typeof L=="function"&&(L=this.emptyMessage(M)),L){var N=[{caption:L,value:""}];this.completions=new I(N),this.openPopup(this.editor,M,S),this.popup.renderer.setStyle("ace_loading",!1),this.popup.renderer.setStyle("ace_empty-message",!0);return}return this.detach()}if(E.length==1&&E[0].value==M&&!E[0].snippet)return this.detach();if(this.autoInsert&&!this.autoShown&&E.length==1)return this.insertMatch(E[0])}this.completions=!$&&this.showLoadingState?new I(R.completionsForLoading.concat(E),w.filterText):w,this.openPopup(this.editor,M,S),this.popup.renderer.setStyle("ace_empty-message",!1),this.popup.renderer.setStyle("ace_loading",!$)}.bind(this)),this.showLoadingState&&!this.autoShown&&(!this.popup||!this.popup.isOpen)&&this.$firstOpenTimer.delay(this.stickySelectionDelay/2)},R.prototype.cancelContextMenu=function(){this.editor.$mouseHandler.cancelContextMenu()},R.prototype.updateDocTooltip=function(){var S=this.popup,t=this.completions.filtered,i=t&&(t[S.getHoveredRow()]||t[S.getRow()]),r=null;if(!i||!this.editor||!this.popup.isOpen)return this.hideDocTooltip();for(var c=this.editor.completers.length,h=0;h<c;h++){var x=this.editor.completers[h];if(x.getDocTooltip&&i.completerId===x.id){r=x.getDocTooltip(i);break}}if(!r&&typeof i!="string"&&(r=i),typeof r=="string"&&(r={docText:r}),!r||!r.docHTML&&!r.docText)return this.hideDocTooltip();this.showDocTooltip(r)},R.prototype.showDocTooltip=function(S){this.tooltipNode||(this.tooltipNode=o.createElement("div"),this.tooltipNode.style.margin="0",this.tooltipNode.style.pointerEvents="auto",this.tooltipNode.style.overscrollBehavior="contain",this.tooltipNode.tabIndex=-1,this.tooltipNode.onblur=this.blurListener.bind(this),this.tooltipNode.onclick=this.onTooltipClick.bind(this),this.tooltipNode.id="doc-tooltip",this.tooltipNode.setAttribute("role","tooltip"),this.tooltipNode.addEventListener("wheel",C));var t=this.editor.renderer.theme;this.tooltipNode.className="ace_tooltip ace_doc-tooltip "+(t.isDark?"ace_dark ":"")+(t.cssClass||"");var i=this.tooltipNode;S.docHTML?i.innerHTML=S.docHTML:S.docText&&(i.textContent=S.docText),i.parentNode||this.popup.container.appendChild(this.tooltipNode);var r=this.popup,c=r.container.getBoundingClientRect();i.style.top=r.container.style.top,i.style.bottom=r.container.style.bottom,i.style.display="block",window.innerWidth-c.right<320?c.left<320?r.isTopdown?(i.style.top=c.bottom+"px",i.style.left=c.left+"px",i.style.right="",i.style.bottom=""):(i.style.top=r.container.offsetTop-i.offsetHeight+"px",i.style.left=c.left+"px",i.style.right="",i.style.bottom=""):(i.style.right=window.innerWidth-c.left+"px",i.style.left=""):(i.style.left=c.right+1+"px",i.style.right="")},R.prototype.hideDocTooltip=function(){if(this.tooltipTimer.cancel(),!!this.tooltipNode){var S=this.tooltipNode;!this.editor.isFocused()&&document.activeElement==S&&this.editor.focus(),this.tooltipNode=null,S.parentNode&&S.parentNode.removeChild(S)}},R.prototype.onTooltipClick=function(S){for(var t=S.target;t&&t!=this.tooltipNode;){if(t.nodeName=="A"&&t.href){t.rel="noreferrer",t.target="_blank";break}t=t.parentNode}},R.prototype.destroy=function(){if(this.detach(),this.popup){this.popup.destroy();var S=this.popup.container;S&&S.parentNode&&S.parentNode.removeChild(S)}this.editor&&this.editor.completer==this&&(this.editor.off("destroy",b),this.editor.completer=null),this.inlineRenderer=this.popup=this.editor=null},R.for=function(S){return S.completer instanceof R||(S.completer&&(S.completer.destroy(),S.completer=null),m.get("sharedPopups")?(R.$sharedInstance||(R.$sharedInstance=new R),S.completer=R.$sharedInstance):(S.completer=new R,S.once("destroy",b))),S.completer},R}();y.prototype.commands={Up:function(R){R.completer.goTo("up")},Down:function(R){R.completer.goTo("down")},"Ctrl-Up|Ctrl-Home":function(R){R.completer.goTo("start")},"Ctrl-Down|Ctrl-End":function(R){R.completer.goTo("end")},Esc:function(R){R.completer.detach()},Return:function(R){return R.completer.insertMatch()},"Shift-Return":function(R){R.completer.insertMatch(null,{deleteSuffix:!0})},Tab:function(R){var S=R.completer.insertMatch();if(S||R.tabstopManager)return S;R.completer.goTo("down")},Backspace:function(R){R.execCommand("backspace");var S=p.getCompletionPrefix(R);!S&&R.completer&&R.completer.detach()},PageUp:function(R){R.completer.popup.gotoPageUp()},PageDown:function(R){R.completer.popup.gotoPageDown()}},y.startCommand={name:"startAutocomplete",exec:function(R,S){var t=y.for(R);t.autoInsert=!1,t.autoSelect=!0,t.autoShown=!1,t.showPopup(R,S),t.cancelContextMenu()},bindKey:"Ctrl-Space|Ctrl-Shift-Space|Alt-Space"};var A=function(){function R(S){this.initialPosition=S,this.active=!0}return R.prototype.insertByIndex=function(S,t,i){return!this.completions||!this.completions.filtered?!1:this.insertMatch(S,this.completions.filtered[t],i)},R.prototype.insertMatch=function(S,t,i){if(!t)return!1;if(S.startOperation({command:{name:"insertMatch"}}),t.completer&&t.completer.insertMatch)t.completer.insertMatch(S,t);else{if(!this.completions)return!1;var r=this.completions.filterText.length,c=0;if(t.range&&t.range.start.row===t.range.end.row&&(r-=this.initialPosition.prefix.length,r+=this.initialPosition.pos.column-t.range.start.column,c+=t.range.end.column-this.initialPosition.pos.column),r||c){var h;S.selection.getAllRanges?h=S.selection.getAllRanges():h=[S.getSelectionRange()];for(var x=0,w;w=h[x];x++)w.start.column-=r,w.end.column+=c,S.session.remove(w)}t.snippet?d.insertSnippet(S,t.snippet):this.$insertString(S,t),t.completer&&t.completer.onInsert&&typeof t.completer.onInsert=="function"&&t.completer.onInsert(S,t),t.command&&t.command==="startAutocomplete"&&S.execCommand(t.command)}return S.endOperation(),!0},R.prototype.$insertString=function(S,t){var i=t.value||t;S.execCommand("insertstring",i)},R.prototype.gatherCompletions=function(S,t){var i=S.getSession(),r=S.getCursorPosition(),c=p.getCompletionPrefix(S),h=[];this.completers=S.completers;var x=S.completers.length;return S.completers.forEach(function(w,$){w.getCompletions(S,i,r,c,function(E,M){w.hideInlinePreview&&(M=M.map(function(L){return Object.assign(L,{hideInlinePreview:w.hideInlinePreview})})),!E&&M&&(h=h.concat(M)),t(null,{prefix:p.getCompletionPrefix(S),matches:h,finished:--x===0})})}),!0},R.prototype.provideCompletions=function(S,t,i){var r=function(w){var $=w.prefix,E=w.matches;this.completions=new I(E),t.exactMatch&&(this.completions.exactMatch=!0),t.ignoreCaption&&(this.completions.ignoreCaption=!0),this.completions.setFilter($),(w.finished||this.completions.filtered.length)&&i(null,this.completions,w.finished)}.bind(this),c=!0,h=null;if(this.gatherCompletions(S,function(w,$){if(this.active){w&&(i(w,[],!0),this.detach());var E=$.prefix;if(E.indexOf($.prefix)===0){if(c){h=$;return}r($)}}}.bind(this)),c=!1,h){var x=h;h=null,r(x)}},R.prototype.detach=function(){this.active=!1,this.completers&&this.completers.forEach(function(S){typeof S.cancel=="function"&&S.cancel()})},R}(),I=function(){function R(S,t){this.all=S,this.filtered=S,this.filterText=t||"",this.exactMatch=!1,this.ignoreCaption=!1}return R.prototype.setFilter=function(S){if(S.length>this.filterText&&S.lastIndexOf(this.filterText,0)===0)var t=this.filtered;else var t=this.all;this.filterText=S,t=this.filterCompletions(t,this.filterText),t=t.sort(function(r,c){return c.exactMatch-r.exactMatch||c.$score-r.$score||(r.caption||r.value).localeCompare(c.caption||c.value)});var i=null;t=t.filter(function(r){var c=r.snippet||r.caption||r.value;return c===i?!1:(i=c,!0)}),this.filtered=t},R.prototype.filterCompletions=function(S,t){var i=[],r=t.toUpperCase(),c=t.toLowerCase();e:for(var h=0,x;x=S[h];h++){var w=!this.ignoreCaption&&x.caption||x.value||x.snippet;if(w){var $=-1,E=0,M=0,L,N;if(this.exactMatch){if(t!==w.substr(0,t.length))continue e}else{var P=w.toLowerCase().indexOf(c);if(P>-1)M=P;else for(var z=0;z<t.length;z++){var j=w.indexOf(c[z],$+1),V=w.indexOf(r[z],$+1);if(L=j>=0&&(V<0||j<V)?j:V,L<0)continue e;N=L-$-1,N>0&&($===-1&&(M+=10),M+=N,E|=1<<z),$=L}}x.matchMask=E,x.exactMatch=M?0:1,x.$score=(x.score||0)-M,i.push(x)}}return i},R}();l.Autocomplete=y,l.CompletionProvider=A,l.FilteredList=I}),ace.define("ace/autocomplete/text_completer",["require","exports","module","ace/range"],function(n,l,T){function e(p,s){var o=p.getTextRange(u.fromPoints({row:0,column:0},s));return o.split(g).length-1}function a(p,s){var o=e(p,s),d=p.getValue().split(g),m=Object.create(null),_=d[o];return d.forEach(function(C,b){if(!(!C||C===_)){var y=Math.abs(o-b),A=d.length-y;m[C]?m[C]=Math.max(A,m[C]):m[C]=A}}),m}var u=n("../range").Range,g=/[^a-zA-Z_0-9\$\-\u00C0-\u1FFF\u2C00-\uD7FF\w]+/;l.getCompletions=function(p,s,o,d,m){var _=a(s,o),C=Object.keys(_);m(null,C.map(function(b){return{caption:b,value:b,score:_[b],meta:"local"}}))}}),ace.define("ace/ext/language_tools",["require","exports","module","ace/snippets","ace/autocomplete","ace/config","ace/lib/lang","ace/autocomplete/util","ace/autocomplete/text_completer","ace/editor","ace/config"],function(n,l,T){var e=n("../snippets").snippetManager,a=n("../autocomplete").Autocomplete,u=n("../config"),g=n("../lib/lang"),p=n("../autocomplete/util"),s=n("../autocomplete/text_completer"),o={getCompletions:function(r,c,h,x,w){if(c.$mode.completer)return c.$mode.completer.getCompletions(r,c,h,x,w);var $=r.session.getState(h.row),E=c.$mode.getCompletions($,c,h,x);E=E.map(function(M){return M.completerId=o.id,M}),w(null,E)},id:"keywordCompleter"},d=function(r){var c={};return r.replace(/\${(\d+)(:(.*?))?}/g,function(h,x,w,$){return c[x]=$||""}).replace(/\$(\d+?)/g,function(h,x){return c[x]})},m={getCompletions:function(r,c,h,x,w){var $=[],E=c.getTokenAt(h.row,h.column);E&&E.type.match(/(tag-name|tag-open|tag-whitespace|attribute-name|attribute-value)\.xml$/)?$.push("html-tag"):$=e.getActiveScopes(r);var M=e.snippetMap,L=[];$.forEach(function(N){for(var P=M[N]||[],z=P.length;z--;){var j=P[z],V=j.name||j.tabTrigger;V&&L.push({caption:V,snippet:j.content,meta:j.tabTrigger&&!j.name?j.tabTrigger+"⇥ ":"snippet",completerId:m.id})}},this),w(null,L)},getDocTooltip:function(r){r.snippet&&!r.docHTML&&(r.docHTML=["<b>",g.escapeHTML(r.caption),"</b>","<hr></hr>",g.escapeHTML(d(r.snippet))].join(""))},id:"snippetCompleter"},_=[m,s,o];l.setCompleters=function(r){_.length=0,r&&_.push.apply(_,r)},l.addCompleter=function(r){_.push(r)},l.textCompleter=s,l.keyWordCompleter=o,l.snippetCompleter=m;var C={name:"expandSnippet",exec:function(r){return e.expandWithTab(r)},bindKey:"Tab"},b=function(r,c){y(c.session.$mode)},y=function(r){typeof r=="string"&&(r=u.$modes[r]),r&&(e.files||(e.files={}),A(r.$id,r.snippetFileId),r.modes&&r.modes.forEach(y))},A=function(r,c){!c||!r||e.files[r]||(e.files[r]={},u.loadModule(c,function(h){h&&(e.files[r]=h,!h.snippets&&h.snippetText&&(h.snippets=e.parseSnippetFile(h.snippetText)),e.register(h.snippets||[],h.scope),h.includeScopes&&(e.snippetMap[h.scope].includeScopes=h.includeScopes,h.includeScopes.forEach(function(x){y("ace/mode/"+x)})))}))},I=function(r){var c=r.editor,h=c.completer&&c.completer.activated;if(r.command.name==="backspace")h&&!p.getCompletionPrefix(c)&&c.completer.detach();else if(r.command.name==="insertstring"&&!h){R=r;var x=r.editor.$liveAutocompletionDelay;x?S.delay(x):t(r)}},R,S=g.delayedCall(function(){t(R)},0),t=function(r){var c=r.editor,h=p.getCompletionPrefix(c),x=r.args,w=p.triggerAutocomplete(c,x);if(h&&h.length>=c.$liveAutocompletionThreshold||w){var $=a.for(c);$.autoShown=!0,$.showPopup(c)}},i=n("../editor").Editor;n("../config").defineOptions(i.prototype,"editor",{enableBasicAutocompletion:{set:function(r){r?(this.completers||(this.completers=Array.isArray(r)?r:_),this.commands.addCommand(a.startCommand)):this.commands.removeCommand(a.startCommand)},value:!1},enableLiveAutocompletion:{set:function(r){r?(this.completers||(this.completers=Array.isArray(r)?r:_),this.commands.on("afterExec",I)):this.commands.off("afterExec",I)},value:!1},liveAutocompletionDelay:{initialValue:0},liveAutocompletionThreshold:{initialValue:0},enableSnippets:{set:function(r){r?(this.commands.addCommand(C),this.on("changeMode",b),b(null,this)):(this.commands.removeCommand(C),this.off("changeMode",b))},value:!1}})}),function(){ace.require(["ace/ext/language_tools"],function(n){k&&(k.exports=n)})}()})(hi);var ui={exports:{}};(function(k,f){ace.define("ace/ext/searchbox-css",["require","exports","module"],function(n,l,T){T.exports=`

/* ------------------------------------------------------------------------------------------
 * Editor Search Form
 * --------------------------------------------------------------------------------------- */
.ace_search {
    background-color: #ddd;
    color: #666;
    border: 1px solid #cbcbcb;
    border-top: 0 none;
    overflow: hidden;
    margin: 0;
    padding: 4px 6px 0 4px;
    position: absolute;
    top: 0;
    z-index: 99;
    white-space: normal;
}
.ace_search.left {
    border-left: 0 none;
    border-radius: 0px 0px 5px 0px;
    left: 0;
}
.ace_search.right {
    border-radius: 0px 0px 0px 5px;
    border-right: 0 none;
    right: 0;
}

.ace_search_form, .ace_replace_form {
    margin: 0 20px 4px 0;
    overflow: hidden;
    line-height: 1.9;
}
.ace_replace_form {
    margin-right: 0;
}
.ace_search_form.ace_nomatch {
    outline: 1px solid red;
}

.ace_search_field {
    border-radius: 3px 0 0 3px;
    background-color: white;
    color: black;
    border: 1px solid #cbcbcb;
    border-right: 0 none;
    outline: 0;
    padding: 0;
    font-size: inherit;
    margin: 0;
    line-height: inherit;
    padding: 0 6px;
    min-width: 17em;
    vertical-align: top;
    min-height: 1.8em;
    box-sizing: content-box;
}
.ace_searchbtn {
    border: 1px solid #cbcbcb;
    line-height: inherit;
    display: inline-block;
    padding: 0 6px;
    background: #fff;
    border-right: 0 none;
    border-left: 1px solid #dcdcdc;
    cursor: pointer;
    margin: 0;
    position: relative;
    color: #666;
}
.ace_searchbtn:last-child {
    border-radius: 0 3px 3px 0;
    border-right: 1px solid #cbcbcb;
}
.ace_searchbtn:disabled {
    background: none;
    cursor: default;
}
.ace_searchbtn:hover {
    background-color: #eef1f6;
}
.ace_searchbtn.prev, .ace_searchbtn.next {
     padding: 0px 0.7em
}
.ace_searchbtn.prev:after, .ace_searchbtn.next:after {
     content: "";
     border: solid 2px #888;
     width: 0.5em;
     height: 0.5em;
     border-width:  2px 0 0 2px;
     display:inline-block;
     transform: rotate(-45deg);
}
.ace_searchbtn.next:after {
     border-width: 0 2px 2px 0 ;
}
.ace_searchbtn_close {
    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAcCAYAAABRVo5BAAAAZ0lEQVR42u2SUQrAMAhDvazn8OjZBilCkYVVxiis8H4CT0VrAJb4WHT3C5xU2a2IQZXJjiQIRMdkEoJ5Q2yMqpfDIo+XY4k6h+YXOyKqTIj5REaxloNAd0xiKmAtsTHqW8sR2W5f7gCu5nWFUpVjZwAAAABJRU5ErkJggg==) no-repeat 50% 0;
    border-radius: 50%;
    border: 0 none;
    color: #656565;
    cursor: pointer;
    font: 16px/16px Arial;
    padding: 0;
    height: 14px;
    width: 14px;
    top: 9px;
    right: 7px;
    position: absolute;
}
.ace_searchbtn_close:hover {
    background-color: #656565;
    background-position: 50% 100%;
    color: white;
}

.ace_button {
    margin-left: 2px;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -o-user-select: none;
    -ms-user-select: none;
    user-select: none;
    overflow: hidden;
    opacity: 0.7;
    border: 1px solid rgba(100,100,100,0.23);
    padding: 1px;
    box-sizing:    border-box!important;
    color: black;
}

.ace_button:hover {
    background-color: #eee;
    opacity:1;
}
.ace_button:active {
    background-color: #ddd;
}

.ace_button.checked {
    border-color: #3399ff;
    opacity:1;
}

.ace_search_options{
    margin-bottom: 3px;
    text-align: right;
    -webkit-user-select: none;
    -moz-user-select: none;
    -o-user-select: none;
    -ms-user-select: none;
    user-select: none;
    clear: both;
}

.ace_search_counter {
    float: left;
    font-family: arial;
    padding: 0 8px;
}`}),ace.define("ace/ext/searchbox",["require","exports","module","ace/lib/dom","ace/lib/lang","ace/lib/event","ace/ext/searchbox-css","ace/keyboard/hash_handler","ace/lib/keys","ace/config"],function(n,l,T){var e=n("../lib/dom"),a=n("../lib/lang"),u=n("../lib/event"),g=n("./searchbox-css"),p=n("../keyboard/hash_handler").HashHandler,s=n("../lib/keys"),o=n("../config").nls,d=999;e.importCssString(g,"ace_searchbox",!1);var m=function(){function b(y,A,I){this.activeInput;var R=e.createElement("div");e.buildDom(["div",{class:"ace_search right"},["span",{action:"hide",class:"ace_searchbtn_close"}],["div",{class:"ace_search_form"},["input",{class:"ace_search_field",placeholder:o("search-box.find.placeholder","Search for"),spellcheck:"false"}],["span",{action:"findPrev",class:"ace_searchbtn prev"},"​"],["span",{action:"findNext",class:"ace_searchbtn next"},"​"],["span",{action:"findAll",class:"ace_searchbtn",title:"Alt-Enter"},o("search-box.find-all.text","All")]],["div",{class:"ace_replace_form"},["input",{class:"ace_search_field",placeholder:o("search-box.replace.placeholder","Replace with"),spellcheck:"false"}],["span",{action:"replaceAndFindNext",class:"ace_searchbtn"},o("search-box.replace-next.text","Replace")],["span",{action:"replaceAll",class:"ace_searchbtn"},o("search-box.replace-all.text","All")]],["div",{class:"ace_search_options"},["span",{action:"toggleReplace",class:"ace_button",title:o("search-box.toggle-replace.title","Toggle Replace mode"),style:"float:left;margin-top:-2px;padding:0 5px;"},"+"],["span",{class:"ace_search_counter"}],["span",{action:"toggleRegexpMode",class:"ace_button",title:o("search-box.toggle-regexp.title","RegExp Search")},".*"],["span",{action:"toggleCaseSensitive",class:"ace_button",title:o("search-box.toggle-case.title","CaseSensitive Search")},"Aa"],["span",{action:"toggleWholeWords",class:"ace_button",title:o("search-box.toggle-whole-word.title","Whole Word Search")},"\\b"],["span",{action:"searchInSelection",class:"ace_button",title:o("search-box.toggle-in-selection.title","Search In Selection")},"S"]]],R),this.element=R.firstChild,this.setSession=this.setSession.bind(this),this.$init(),this.setEditor(y),e.importCssString(g,"ace_searchbox",y.container)}return b.prototype.setEditor=function(y){y.searchBox=this,y.renderer.scroller.appendChild(this.element),this.editor=y},b.prototype.setSession=function(y){this.searchRange=null,this.$syncOptions(!0)},b.prototype.$initElements=function(y){this.searchBox=y.querySelector(".ace_search_form"),this.replaceBox=y.querySelector(".ace_replace_form"),this.searchOption=y.querySelector("[action=searchInSelection]"),this.replaceOption=y.querySelector("[action=toggleReplace]"),this.regExpOption=y.querySelector("[action=toggleRegexpMode]"),this.caseSensitiveOption=y.querySelector("[action=toggleCaseSensitive]"),this.wholeWordOption=y.querySelector("[action=toggleWholeWords]"),this.searchInput=this.searchBox.querySelector(".ace_search_field"),this.replaceInput=this.replaceBox.querySelector(".ace_search_field"),this.searchCounter=y.querySelector(".ace_search_counter")},b.prototype.$init=function(){var y=this.element;this.$initElements(y);var A=this;u.addListener(y,"mousedown",function(I){setTimeout(function(){A.activeInput.focus()},0),u.stopPropagation(I)}),u.addListener(y,"click",function(I){var R=I.target||I.srcElement,S=R.getAttribute("action");S&&A[S]?A[S]():A.$searchBarKb.commands[S]&&A.$searchBarKb.commands[S].exec(A),u.stopPropagation(I)}),u.addCommandKeyListener(y,function(I,R,S){var t=s.keyCodeToString(S),i=A.$searchBarKb.findKeyCommand(R,t);i&&i.exec&&(i.exec(A),u.stopEvent(I))}),this.$onChange=a.delayedCall(function(){A.find(!1,!1)}),u.addListener(this.searchInput,"input",function(){A.$onChange.schedule(20)}),u.addListener(this.searchInput,"focus",function(){A.activeInput=A.searchInput,A.searchInput.value&&A.highlight()}),u.addListener(this.replaceInput,"focus",function(){A.activeInput=A.replaceInput,A.searchInput.value&&A.highlight()})},b.prototype.setSearchRange=function(y){this.searchRange=y,y?this.searchRangeMarker=this.editor.session.addMarker(y,"ace_active-line"):this.searchRangeMarker&&(this.editor.session.removeMarker(this.searchRangeMarker),this.searchRangeMarker=null)},b.prototype.$syncOptions=function(y){e.setCssClass(this.replaceOption,"checked",this.searchRange),e.setCssClass(this.searchOption,"checked",this.searchOption.checked),this.replaceOption.textContent=this.replaceOption.checked?"-":"+",e.setCssClass(this.regExpOption,"checked",this.regExpOption.checked),e.setCssClass(this.wholeWordOption,"checked",this.wholeWordOption.checked),e.setCssClass(this.caseSensitiveOption,"checked",this.caseSensitiveOption.checked);var A=this.editor.getReadOnly();this.replaceOption.style.display=A?"none":"",this.replaceBox.style.display=this.replaceOption.checked&&!A?"":"none",this.find(!1,!1,y)},b.prototype.highlight=function(y){this.editor.session.highlight(y||this.editor.$search.$options.re),this.editor.renderer.updateBackMarkers()},b.prototype.find=function(y,A,I){var R=this.editor.find(this.searchInput.value,{skipCurrent:y,backwards:A,wrap:!0,regExp:this.regExpOption.checked,caseSensitive:this.caseSensitiveOption.checked,wholeWord:this.wholeWordOption.checked,preventScroll:I,range:this.searchRange}),S=!R&&this.searchInput.value;e.setCssClass(this.searchBox,"ace_nomatch",S),this.editor._emit("findSearchBox",{match:!S}),this.highlight(),this.updateCounter()},b.prototype.updateCounter=function(){var y=this.editor,A=y.$search.$options.re,I=A.unicode,R=0,S=0;if(A){var t=this.searchRange?y.session.getTextRange(this.searchRange):y.getValue(),i=y.session.doc.positionToIndex(y.selection.anchor);this.searchRange&&(i-=y.session.doc.positionToIndex(this.searchRange.start));for(var r=A.lastIndex=0,c;(c=A.exec(t))&&(R++,r=c.index,r<=i&&S++,!(R>d||!c[0]&&(A.lastIndex=r+=a.skipEmptyMatch(t,r,I),r>=t.length))););}this.searchCounter.textContent=o("search-box.search-counter","$0 of $1",[S,R>d?d+"+":R])},b.prototype.findNext=function(){this.find(!0,!1)},b.prototype.findPrev=function(){this.find(!0,!0)},b.prototype.findAll=function(){var y=this.editor.findAll(this.searchInput.value,{regExp:this.regExpOption.checked,caseSensitive:this.caseSensitiveOption.checked,wholeWord:this.wholeWordOption.checked}),A=!y&&this.searchInput.value;e.setCssClass(this.searchBox,"ace_nomatch",A),this.editor._emit("findSearchBox",{match:!A}),this.highlight(),this.hide()},b.prototype.replace=function(){this.editor.getReadOnly()||this.editor.replace(this.replaceInput.value)},b.prototype.replaceAndFindNext=function(){this.editor.getReadOnly()||(this.editor.replace(this.replaceInput.value),this.findNext())},b.prototype.replaceAll=function(){this.editor.getReadOnly()||this.editor.replaceAll(this.replaceInput.value)},b.prototype.hide=function(){this.active=!1,this.setSearchRange(null),this.editor.off("changeSession",this.setSession),this.element.style.display="none",this.editor.keyBinding.removeKeyboardHandler(this.$closeSearchBarKb),this.editor.focus()},b.prototype.show=function(y,A){this.active=!0,this.editor.on("changeSession",this.setSession),this.element.style.display="",this.replaceOption.checked=A,y&&(this.searchInput.value=y),this.searchInput.focus(),this.searchInput.select(),this.editor.keyBinding.addKeyboardHandler(this.$closeSearchBarKb),this.$syncOptions(!0)},b.prototype.isFocused=function(){var y=document.activeElement;return y==this.searchInput||y==this.replaceInput},b}(),_=new p;_.bindKeys({"Ctrl-f|Command-f":function(b){var y=b.isReplace=!b.isReplace;b.replaceBox.style.display=y?"":"none",b.replaceOption.checked=!1,b.$syncOptions(),b.searchInput.focus()},"Ctrl-H|Command-Option-F":function(b){b.editor.getReadOnly()||(b.replaceOption.checked=!0,b.$syncOptions(),b.replaceInput.focus())},"Ctrl-G|Command-G":function(b){b.findNext()},"Ctrl-Shift-G|Command-Shift-G":function(b){b.findPrev()},esc:function(b){setTimeout(function(){b.hide()})},Return:function(b){b.activeInput==b.replaceInput&&b.replace(),b.findNext()},"Shift-Return":function(b){b.activeInput==b.replaceInput&&b.replace(),b.findPrev()},"Alt-Return":function(b){b.activeInput==b.replaceInput&&b.replaceAll(),b.findAll()},Tab:function(b){(b.activeInput==b.replaceInput?b.searchInput:b.replaceInput).focus()}}),_.addCommands([{name:"toggleRegexpMode",bindKey:{win:"Alt-R|Alt-/",mac:"Ctrl-Alt-R|Ctrl-Alt-/"},exec:function(b){b.regExpOption.checked=!b.regExpOption.checked,b.$syncOptions()}},{name:"toggleCaseSensitive",bindKey:{win:"Alt-C|Alt-I",mac:"Ctrl-Alt-R|Ctrl-Alt-I"},exec:function(b){b.caseSensitiveOption.checked=!b.caseSensitiveOption.checked,b.$syncOptions()}},{name:"toggleWholeWords",bindKey:{win:"Alt-B|Alt-W",mac:"Ctrl-Alt-B|Ctrl-Alt-W"},exec:function(b){b.wholeWordOption.checked=!b.wholeWordOption.checked,b.$syncOptions()}},{name:"toggleReplace",exec:function(b){b.replaceOption.checked=!b.replaceOption.checked,b.$syncOptions()}},{name:"searchInSelection",exec:function(b){b.searchOption.checked=!b.searchRange,b.setSearchRange(b.searchOption.checked&&b.editor.getSelectionRange()),b.$syncOptions()}}]);var C=new p([{bindKey:"Esc",name:"closeSearchBar",exec:function(b){b.searchBox.hide()}}]);m.prototype.$searchBarKb=_,m.prototype.$closeSearchBarKb=C,l.SearchBox=m,l.Search=function(b,y){var A=b.searchBox||new m(b);A.show(b.session.getTextRange(),y)}}),function(){ace.require(["ace/ext/searchbox"],function(n){k&&(k.exports=n)})}()})(ui);var De={},$t={},it={exports:{}};it.exports;(function(k,f){var n=200,l="__lodash_hash_undefined__",T=1,e=2,a=9007199254740991,u="[object Arguments]",g="[object Array]",p="[object AsyncFunction]",s="[object Boolean]",o="[object Date]",d="[object Error]",m="[object Function]",_="[object GeneratorFunction]",C="[object Map]",b="[object Number]",y="[object Null]",A="[object Object]",I="[object Promise]",R="[object Proxy]",S="[object RegExp]",t="[object Set]",i="[object String]",r="[object Symbol]",c="[object Undefined]",h="[object WeakMap]",x="[object ArrayBuffer]",w="[object DataView]",$="[object Float32Array]",E="[object Float64Array]",M="[object Int8Array]",L="[object Int16Array]",N="[object Int32Array]",P="[object Uint8Array]",z="[object Uint8ClampedArray]",j="[object Uint16Array]",V="[object Uint32Array]",ee=/[\\^$.*+?()[\]{}|]/g,Z=/^\[object .+?Constructor\]$/,ie=/^(?:0|[1-9]\d*)$/,K={};K[$]=K[E]=K[M]=K[L]=K[N]=K[P]=K[z]=K[j]=K[V]=!0,K[u]=K[g]=K[x]=K[s]=K[w]=K[o]=K[d]=K[m]=K[C]=K[b]=K[A]=K[S]=K[t]=K[i]=K[h]=!1;var D=typeof re=="object"&&re&&re.Object===Object&&re,X=typeof self=="object"&&self&&self.Object===Object&&self,J=D||X||Function("return this")(),U=f&&!f.nodeType&&f,ue=U&&!0&&k&&!k.nodeType&&k,de=ue&&ue.exports===U,Ce=de&&D.process,Fe=function(){try{return Ce&&Ce.binding&&Ce.binding("util")}catch(v){}}(),Mt=Fe&&Fe.isTypedArray;function $n(v,O){for(var F=-1,B=v==null?0:v.length,Q=0,G=[];++F<B;){var ne=v[F];O(ne,F,v)&&(G[Q++]=ne)}return G}function Sn(v,O){for(var F=-1,B=O.length,Q=v.length;++F<B;)v[Q+F]=O[F];return v}function Cn(v,O){for(var F=-1,B=v==null?0:v.length;++F<B;)if(O(v[F],F,v))return!0;return!1}function Tn(v,O){for(var F=-1,B=Array(v);++F<v;)B[F]=O(F);return B}function En(v){return function(O){return v(O)}}function An(v,O){return v.has(O)}function Mn(v,O){return v==null?void 0:v[O]}function Rn(v){var O=-1,F=Array(v.size);return v.forEach(function(B,Q){F[++O]=[Q,B]}),F}function On(v,O){return function(F){return v(O(F))}}function Ln(v){var O=-1,F=Array(v.size);return v.forEach(function(B){F[++O]=B}),F}var In=Array.prototype,Pn=Function.prototype,Ke=Object.prototype,lt=J["__core-js_shared__"],Rt=Pn.toString,ve=Ke.hasOwnProperty,Ot=function(){var v=/[^.]+$/.exec(lt&&lt.keys&&lt.keys.IE_PROTO||"");return v?"Symbol(src)_1."+v:""}(),Lt=Ke.toString,Fn=RegExp("^"+Rt.call(ve).replace(ee,"\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g,"$1.*?")+"$"),It=de?J.Buffer:void 0,Xe=J.Symbol,Pt=J.Uint8Array,Ft=Ke.propertyIsEnumerable,Nn=In.splice,Te=Xe?Xe.toStringTag:void 0,Nt=Object.getOwnPropertySymbols,zn=It?It.isBuffer:void 0,Dn=On(Object.keys,Object),ct=Ne(J,"DataView"),je=Ne(J,"Map"),pt=Ne(J,"Promise"),ht=Ne(J,"Set"),ut=Ne(J,"WeakMap"),He=Ne(Object,"create"),Bn=Me(ct),jn=Me(je),Hn=Me(pt),qn=Me(ht),Un=Me(ut),zt=Xe?Xe.prototype:void 0,dt=zt?zt.valueOf:void 0;function Ee(v){var O=-1,F=v==null?0:v.length;for(this.clear();++O<F;){var B=v[O];this.set(B[0],B[1])}}function Wn(){this.__data__=He?He(null):{},this.size=0}function Vn(v){var O=this.has(v)&&delete this.__data__[v];return this.size-=O?1:0,O}function Gn(v){var O=this.__data__;if(He){var F=O[v];return F===l?void 0:F}return ve.call(O,v)?O[v]:void 0}function Kn(v){var O=this.__data__;return He?O[v]!==void 0:ve.call(O,v)}function Xn(v,O){var F=this.__data__;return this.size+=this.has(v)?0:1,F[v]=He&&O===void 0?l:O,this}Ee.prototype.clear=Wn,Ee.prototype.delete=Vn,Ee.prototype.get=Gn,Ee.prototype.has=Kn,Ee.prototype.set=Xn;function xe(v){var O=-1,F=v==null?0:v.length;for(this.clear();++O<F;){var B=v[O];this.set(B[0],B[1])}}function Yn(){this.__data__=[],this.size=0}function Jn(v){var O=this.__data__,F=Je(O,v);if(F<0)return!1;var B=O.length-1;return F==B?O.pop():Nn.call(O,F,1),--this.size,!0}function Zn(v){var O=this.__data__,F=Je(O,v);return F<0?void 0:O[F][1]}function Qn(v){return Je(this.__data__,v)>-1}function er(v,O){var F=this.__data__,B=Je(F,v);return B<0?(++this.size,F.push([v,O])):F[B][1]=O,this}xe.prototype.clear=Yn,xe.prototype.delete=Jn,xe.prototype.get=Zn,xe.prototype.has=Qn,xe.prototype.set=er;function Ae(v){var O=-1,F=v==null?0:v.length;for(this.clear();++O<F;){var B=v[O];this.set(B[0],B[1])}}function tr(){this.size=0,this.__data__={hash:new Ee,map:new(je||xe),string:new Ee}}function nr(v){var O=Ze(this,v).delete(v);return this.size-=O?1:0,O}function rr(v){return Ze(this,v).get(v)}function ir(v){return Ze(this,v).has(v)}function or(v,O){var F=Ze(this,v),B=F.size;return F.set(v,O),this.size+=F.size==B?0:1,this}Ae.prototype.clear=tr,Ae.prototype.delete=nr,Ae.prototype.get=rr,Ae.prototype.has=ir,Ae.prototype.set=or;function Ye(v){var O=-1,F=v==null?0:v.length;for(this.__data__=new Ae;++O<F;)this.add(v[O])}function sr(v){return this.__data__.set(v,l),this}function ar(v){return this.__data__.has(v)}Ye.prototype.add=Ye.prototype.push=sr,Ye.prototype.has=ar;function ke(v){var O=this.__data__=new xe(v);this.size=O.size}function lr(){this.__data__=new xe,this.size=0}function cr(v){var O=this.__data__,F=O.delete(v);return this.size=O.size,F}function pr(v){return this.__data__.get(v)}function hr(v){return this.__data__.has(v)}function ur(v,O){var F=this.__data__;if(F instanceof xe){var B=F.__data__;if(!je||B.length<n-1)return B.push([v,O]),this.size=++F.size,this;F=this.__data__=new Ae(B)}return F.set(v,O),this.size=F.size,this}ke.prototype.clear=lr,ke.prototype.delete=cr,ke.prototype.get=pr,ke.prototype.has=hr,ke.prototype.set=ur;function dr(v,O){var F=Qe(v),B=!F&&Er(v),Q=!F&&!B&&gt(v),G=!F&&!B&&!Q&&Gt(v),ne=F||B||Q||G,oe=ne?Tn(v.length,String):[],se=oe.length;for(var te in v)(O||ve.call(v,te))&&!(ne&&(te=="length"||Q&&(te=="offset"||te=="parent")||G&&(te=="buffer"||te=="byteLength"||te=="byteOffset")||kr(te,se)))&&oe.push(te);return oe}function Je(v,O){for(var F=v.length;F--;)if(qt(v[F][0],O))return F;return-1}function gr(v,O,F){var B=O(v);return Qe(v)?B:Sn(B,F(v))}function qe(v){return v==null?v===void 0?c:y:Te&&Te in Object(v)?wr(v):Tr(v)}function Dt(v){return Ue(v)&&qe(v)==u}function Bt(v,O,F,B,Q){return v===O?!0:v==null||O==null||!Ue(v)&&!Ue(O)?v!==v&&O!==O:fr(v,O,F,B,Bt,Q)}function fr(v,O,F,B,Q,G){var ne=Qe(v),oe=Qe(O),se=ne?g:$e(v),te=oe?g:$e(O);se=se==u?A:se,te=te==u?A:te;var pe=se==A,me=te==A,ae=se==te;if(ae&&gt(v)){if(!gt(O))return!1;ne=!0,pe=!1}if(ae&&!pe)return G||(G=new ke),ne||Gt(v)?jt(v,O,F,B,Q,G):yr(v,O,se,F,B,Q,G);if(!(F&T)){var ge=pe&&ve.call(v,"__wrapped__"),fe=me&&ve.call(O,"__wrapped__");if(ge||fe){var Se=ge?v.value():v,we=fe?O.value():O;return G||(G=new ke),Q(Se,we,F,B,G)}}return ae?(G||(G=new ke),xr(v,O,F,B,Q,G)):!1}function mr(v){if(!Vt(v)||Sr(v))return!1;var O=Ut(v)?Fn:Z;return O.test(Me(v))}function br(v){return Ue(v)&&Wt(v.length)&&!!K[qe(v)]}function vr(v){if(!Cr(v))return Dn(v);var O=[];for(var F in Object(v))ve.call(v,F)&&F!="constructor"&&O.push(F);return O}function jt(v,O,F,B,Q,G){var ne=F&T,oe=v.length,se=O.length;if(oe!=se&&!(ne&&se>oe))return!1;var te=G.get(v);if(te&&G.get(O))return te==O;var pe=-1,me=!0,ae=F&e?new Ye:void 0;for(G.set(v,O),G.set(O,v);++pe<oe;){var ge=v[pe],fe=O[pe];if(B)var Se=ne?B(fe,ge,pe,O,v,G):B(ge,fe,pe,v,O,G);if(Se!==void 0){if(Se)continue;me=!1;break}if(ae){if(!Cn(O,function(we,Re){if(!An(ae,Re)&&(ge===we||Q(ge,we,F,B,G)))return ae.push(Re)})){me=!1;break}}else if(!(ge===fe||Q(ge,fe,F,B,G))){me=!1;break}}return G.delete(v),G.delete(O),me}function yr(v,O,F,B,Q,G,ne){switch(F){case w:if(v.byteLength!=O.byteLength||v.byteOffset!=O.byteOffset)return!1;v=v.buffer,O=O.buffer;case x:return!(v.byteLength!=O.byteLength||!G(new Pt(v),new Pt(O)));case s:case o:case b:return qt(+v,+O);case d:return v.name==O.name&&v.message==O.message;case S:case i:return v==O+"";case C:var oe=Rn;case t:var se=B&T;if(oe||(oe=Ln),v.size!=O.size&&!se)return!1;var te=ne.get(v);if(te)return te==O;B|=e,ne.set(v,O);var pe=jt(oe(v),oe(O),B,Q,G,ne);return ne.delete(v),pe;case r:if(dt)return dt.call(v)==dt.call(O)}return!1}function xr(v,O,F,B,Q,G){var ne=F&T,oe=Ht(v),se=oe.length,te=Ht(O),pe=te.length;if(se!=pe&&!ne)return!1;for(var me=se;me--;){var ae=oe[me];if(!(ne?ae in O:ve.call(O,ae)))return!1}var ge=G.get(v);if(ge&&G.get(O))return ge==O;var fe=!0;G.set(v,O),G.set(O,v);for(var Se=ne;++me<se;){ae=oe[me];var we=v[ae],Re=O[ae];if(B)var Kt=ne?B(Re,we,ae,O,v,G):B(we,Re,ae,v,O,G);if(!(Kt===void 0?we===Re||Q(we,Re,F,B,G):Kt)){fe=!1;break}Se||(Se=ae=="constructor")}if(fe&&!Se){var et=v.constructor,tt=O.constructor;et!=tt&&"constructor"in v&&"constructor"in O&&!(typeof et=="function"&&et instanceof et&&typeof tt=="function"&&tt instanceof tt)&&(fe=!1)}return G.delete(v),G.delete(O),fe}function Ht(v){return gr(v,Rr,_r)}function Ze(v,O){var F=v.__data__;return $r(O)?F[typeof O=="string"?"string":"hash"]:F.map}function Ne(v,O){var F=Mn(v,O);return mr(F)?F:void 0}function wr(v){var O=ve.call(v,Te),F=v[Te];try{v[Te]=void 0;var B=!0}catch(G){}var Q=Lt.call(v);return B&&(O?v[Te]=F:delete v[Te]),Q}var _r=Nt?function(v){return v==null?[]:(v=Object(v),$n(Nt(v),function(O){return Ft.call(v,O)}))}:Or,$e=qe;(ct&&$e(new ct(new ArrayBuffer(1)))!=w||je&&$e(new je)!=C||pt&&$e(pt.resolve())!=I||ht&&$e(new ht)!=t||ut&&$e(new ut)!=h)&&($e=function(v){var O=qe(v),F=O==A?v.constructor:void 0,B=F?Me(F):"";if(B)switch(B){case Bn:return w;case jn:return C;case Hn:return I;case qn:return t;case Un:return h}return O});function kr(v,O){return O=O==null?a:O,!!O&&(typeof v=="number"||ie.test(v))&&v>-1&&v%1==0&&v<O}function $r(v){var O=typeof v;return O=="string"||O=="number"||O=="symbol"||O=="boolean"?v!=="__proto__":v===null}function Sr(v){return!!Ot&&Ot in v}function Cr(v){var O=v&&v.constructor,F=typeof O=="function"&&O.prototype||Ke;return v===F}function Tr(v){return Lt.call(v)}function Me(v){if(v!=null){try{return Rt.call(v)}catch(O){}try{return v+""}catch(O){}}return""}function qt(v,O){return v===O||v!==v&&O!==O}var Er=Dt(function(){return arguments}())?Dt:function(v){return Ue(v)&&ve.call(v,"callee")&&!Ft.call(v,"callee")},Qe=Array.isArray;function Ar(v){return v!=null&&Wt(v.length)&&!Ut(v)}var gt=zn||Lr;function Mr(v,O){return Bt(v,O)}function Ut(v){if(!Vt(v))return!1;var O=qe(v);return O==m||O==_||O==p||O==R}function Wt(v){return typeof v=="number"&&v>-1&&v%1==0&&v<=a}function Vt(v){var O=typeof v;return v!=null&&(O=="object"||O=="function")}function Ue(v){return v!=null&&typeof v=="object"}var Gt=Mt?En(Mt):br;function Rr(v){return Ar(v)?dr(v):vr(v)}function Or(){return[]}function Lr(){return!1}k.exports=Mr})(it,it.exports);var un=it.exports,be={};Object.defineProperty(be,"__esModule",{value:!0});be.getAceInstance=be.debounce=be.editorEvents=be.editorOptions=void 0;var di=["minLines","maxLines","readOnly","highlightActiveLine","tabSize","enableBasicAutocompletion","enableLiveAutocompletion","enableSnippets"];be.editorOptions=di;var gi=["onChange","onFocus","onInput","onBlur","onCopy","onPaste","onSelectionChange","onCursorChange","onScroll","handleOptions","updateRef"];be.editorEvents=gi;var fi=function(){var k;return typeof window=="undefined"?(re.window={},k=rt,delete re.window):window.ace?(k=window.ace,k.acequire=window.ace.require||window.ace.acequire):k=rt,k};be.getAceInstance=fi;var mi=function(k,f){var n=null;return function(){var l=this,T=arguments;clearTimeout(n),n=setTimeout(function(){k.apply(l,T)},f)}};be.debounce=mi;var bi=re&&re.__extends||function(){var k=function(f,n){return k=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(l,T){l.__proto__=T}||function(l,T){for(var e in T)Object.prototype.hasOwnProperty.call(T,e)&&(l[e]=T[e])},k(f,n)};return function(f,n){if(typeof n!="function"&&n!==null)throw new TypeError("Class extends value "+String(n)+" is not a constructor or null");k(f,n);function l(){this.constructor=f}f.prototype=n===null?Object.create(n):(l.prototype=n.prototype,new l)}}(),wt=re&&re.__assign||function(){return wt=Object.assign||function(k){for(var f,n=1,l=arguments.length;n<l;n++){f=arguments[n];for(var T in f)Object.prototype.hasOwnProperty.call(f,T)&&(k[T]=f[T])}return k},wt.apply(this,arguments)};Object.defineProperty($t,"__esModule",{value:!0});var vi=rt,H=kt,en=he,nt=un,ze=be,tn=(0,ze.getAceInstance)(),yi=function(k){bi(f,k);function f(n){var l=k.call(this,n)||this;return ze.editorEvents.forEach(function(T){l[T]=l[T].bind(l)}),l.debounce=ze.debounce,l}return f.prototype.isInShadow=function(n){for(var l=n&&n.parentNode;l;){if(l.toString()==="[object ShadowRoot]")return!0;l=l.parentNode}return!1},f.prototype.componentDidMount=function(){var n=this,l=this.props,T=l.className,e=l.onBeforeLoad,a=l.onValidate,u=l.mode,g=l.focus,p=l.theme,s=l.fontSize,o=l.value,d=l.defaultValue,m=l.showGutter,_=l.wrapEnabled,C=l.showPrintMargin,b=l.scrollMargin,y=b===void 0?[0,0,0,0]:b,A=l.keyboardHandler,I=l.onLoad,R=l.commands,S=l.annotations,t=l.markers,i=l.placeholder;this.editor=tn.edit(this.refEditor),e&&e(tn);for(var r=Object.keys(this.props.editorProps),c=0;c<r.length;c++)this.editor[r[c]]=this.props.editorProps[r[c]];this.props.debounceChangePeriod&&(this.onChange=this.debounce(this.onChange,this.props.debounceChangePeriod)),this.editor.renderer.setScrollMargin(y[0],y[1],y[2],y[3]),this.isInShadow(this.refEditor)&&this.editor.renderer.attachToShadowRoot(),this.editor.getSession().setMode(typeof u=="string"?"ace/mode/".concat(u):u),p&&p!==""&&this.editor.setTheme("ace/theme/".concat(p)),this.editor.setFontSize(typeof s=="number"?"".concat(s,"px"):s),this.editor.getSession().setValue(d||o||""),this.props.navigateToFileEnd&&this.editor.navigateFileEnd(),this.editor.renderer.setShowGutter(m),this.editor.getSession().setUseWrapMode(_),this.editor.setShowPrintMargin(C),this.editor.on("focus",this.onFocus),this.editor.on("blur",this.onBlur),this.editor.on("copy",this.onCopy),this.editor.on("paste",this.onPaste),this.editor.on("change",this.onChange),this.editor.on("input",this.onInput),i&&this.updatePlaceholder(),this.editor.getSession().selection.on("changeSelection",this.onSelectionChange),this.editor.getSession().selection.on("changeCursor",this.onCursorChange),a&&this.editor.getSession().on("changeAnnotation",function(){var x=n.editor.getSession().getAnnotations();n.props.onValidate(x)}),this.editor.session.on("changeScrollTop",this.onScroll),this.editor.getSession().setAnnotations(S||[]),t&&t.length>0&&this.handleMarkers(t);var h=this.editor.$options;ze.editorOptions.forEach(function(x){h.hasOwnProperty(x)?n.editor.setOption(x,n.props[x]):n.props[x]&&console.warn("ReactAce: editor option ".concat(x," was activated but not found. Did you need to import a related tool or did you possibly mispell the option?"))}),this.handleOptions(this.props),Array.isArray(R)&&R.forEach(function(x){typeof x.exec=="string"?n.editor.commands.bindKey(x.bindKey,x.exec):n.editor.commands.addCommand(x)}),A&&this.editor.setKeyboardHandler("ace/keyboard/"+A),T&&(this.refEditor.className+=" "+T),I&&I(this.editor),this.editor.resize(),g&&this.editor.focus()},f.prototype.componentDidUpdate=function(n){for(var l=n,T=this.props,e=0;e<ze.editorOptions.length;e++){var a=ze.editorOptions[e];T[a]!==l[a]&&this.editor.setOption(a,T[a])}if(T.className!==l.className){var u=this.refEditor.className,g=u.trim().split(" "),p=l.className.trim().split(" ");p.forEach(function(d){var m=g.indexOf(d);g.splice(m,1)}),this.refEditor.className=" "+T.className+" "+g.join(" ")}var s=this.editor&&T.value!=null&&this.editor.getValue()!==T.value;if(s){this.silent=!0;var o=this.editor.session.selection.toJSON();this.editor.setValue(T.value,T.cursorStart),this.editor.session.selection.fromJSON(o),this.silent=!1}T.placeholder!==l.placeholder&&this.updatePlaceholder(),T.mode!==l.mode&&this.editor.getSession().setMode(typeof T.mode=="string"?"ace/mode/".concat(T.mode):T.mode),T.theme!==l.theme&&this.editor.setTheme("ace/theme/"+T.theme),T.keyboardHandler!==l.keyboardHandler&&(T.keyboardHandler?this.editor.setKeyboardHandler("ace/keyboard/"+T.keyboardHandler):this.editor.setKeyboardHandler(null)),T.fontSize!==l.fontSize&&this.editor.setFontSize(typeof T.fontSize=="number"?"".concat(T.fontSize,"px"):T.fontSize),T.wrapEnabled!==l.wrapEnabled&&this.editor.getSession().setUseWrapMode(T.wrapEnabled),T.showPrintMargin!==l.showPrintMargin&&this.editor.setShowPrintMargin(T.showPrintMargin),T.showGutter!==l.showGutter&&this.editor.renderer.setShowGutter(T.showGutter),nt(T.setOptions,l.setOptions)||this.handleOptions(T),(s||!nt(T.annotations,l.annotations))&&this.editor.getSession().setAnnotations(T.annotations||[]),!nt(T.markers,l.markers)&&Array.isArray(T.markers)&&this.handleMarkers(T.markers),nt(T.scrollMargin,l.scrollMargin)||this.handleScrollMargins(T.scrollMargin),(n.height!==this.props.height||n.width!==this.props.width)&&this.editor.resize(),this.props.focus&&!n.focus&&this.editor.focus()},f.prototype.handleScrollMargins=function(n){n===void 0&&(n=[0,0,0,0]),this.editor.renderer.setScrollMargin(n[0],n[1],n[2],n[3])},f.prototype.componentWillUnmount=function(){this.editor&&(this.editor.destroy(),this.editor=null)},f.prototype.onChange=function(n){if(this.props.onChange&&!this.silent){var l=this.editor.getValue();this.props.onChange(l,n)}},f.prototype.onSelectionChange=function(n){if(this.props.onSelectionChange){var l=this.editor.getSelection();this.props.onSelectionChange(l,n)}},f.prototype.onCursorChange=function(n){if(this.props.onCursorChange){var l=this.editor.getSelection();this.props.onCursorChange(l,n)}},f.prototype.onInput=function(n){this.props.onInput&&this.props.onInput(n),this.props.placeholder&&this.updatePlaceholder()},f.prototype.onFocus=function(n){this.props.onFocus&&this.props.onFocus(n,this.editor)},f.prototype.onBlur=function(n){this.props.onBlur&&this.props.onBlur(n,this.editor)},f.prototype.onCopy=function(n){var l=n.text;this.props.onCopy&&this.props.onCopy(l)},f.prototype.onPaste=function(n){var l=n.text;this.props.onPaste&&this.props.onPaste(l)},f.prototype.onScroll=function(){this.props.onScroll&&this.props.onScroll(this.editor)},f.prototype.handleOptions=function(n){for(var l=Object.keys(n.setOptions),T=0;T<l.length;T++)this.editor.setOption(l[T],n.setOptions[l[T]])},f.prototype.handleMarkers=function(n){var l=this,T=this.editor.getSession().getMarkers(!0);for(var e in T)T.hasOwnProperty(e)&&this.editor.getSession().removeMarker(T[e].id);T=this.editor.getSession().getMarkers(!1);for(var e in T)T.hasOwnProperty(e)&&T[e].clazz!=="ace_active-line"&&T[e].clazz!=="ace_selected-word"&&this.editor.getSession().removeMarker(T[e].id);n.forEach(function(a){var u=a.startRow,g=a.startCol,p=a.endRow,s=a.endCol,o=a.className,d=a.type,m=a.inFront,_=m===void 0?!1:m,C=new vi.Range(u,g,p,s);l.editor.getSession().addMarker(C,o,d,_)})},f.prototype.updatePlaceholder=function(){var n=this.editor,l=this.props.placeholder,T=!n.session.getValue().length,e=n.renderer.placeholderNode;!T&&e?(n.renderer.scroller.removeChild(n.renderer.placeholderNode),n.renderer.placeholderNode=null):T&&!e?(e=n.renderer.placeholderNode=document.createElement("div"),e.textContent=l||"",e.className="ace_comment ace_placeholder",e.style.padding="0 9px",e.style.position="absolute",e.style.zIndex="3",n.renderer.scroller.appendChild(e)):T&&e&&(e.textContent=l)},f.prototype.updateRef=function(n){this.refEditor=n},f.prototype.render=function(){var n=this.props,l=n.name,T=n.width,e=n.height,a=n.style,u=wt({width:T,height:e},a);return en.createElement("div",{ref:this.updateRef,id:l,style:u})},f.propTypes={mode:H.oneOfType([H.string,H.object]),focus:H.bool,theme:H.string,name:H.string,className:H.string,height:H.string,width:H.string,fontSize:H.oneOfType([H.number,H.string]),showGutter:H.bool,onChange:H.func,onCopy:H.func,onPaste:H.func,onFocus:H.func,onInput:H.func,onBlur:H.func,onScroll:H.func,value:H.string,defaultValue:H.string,onLoad:H.func,onSelectionChange:H.func,onCursorChange:H.func,onBeforeLoad:H.func,onValidate:H.func,minLines:H.number,maxLines:H.number,readOnly:H.bool,highlightActiveLine:H.bool,tabSize:H.number,showPrintMargin:H.bool,cursorStart:H.number,debounceChangePeriod:H.number,editorProps:H.object,setOptions:H.object,style:H.object,scrollMargin:H.array,annotations:H.array,markers:H.array,keyboardHandler:H.string,wrapEnabled:H.bool,enableSnippets:H.bool,enableBasicAutocompletion:H.oneOfType([H.bool,H.array]),enableLiveAutocompletion:H.oneOfType([H.bool,H.array]),navigateToFileEnd:H.bool,commands:H.array,placeholder:H.string},f.defaultProps={name:"ace-editor",focus:!1,mode:"",theme:"",height:"500px",width:"500px",fontSize:12,enableSnippets:!1,showGutter:!0,onChange:null,onPaste:null,onLoad:null,onScroll:null,minLines:null,maxLines:null,readOnly:!1,highlightActiveLine:!0,showPrintMargin:!0,tabSize:4,cursorStart:1,editorProps:{},style:{},scrollMargin:[0,0,0,0],setOptions:{},wrapEnabled:!1,enableBasicAutocompletion:!1,enableLiveAutocompletion:!1,placeholder:null,navigateToFileEnd:!0},f}(en.Component);$t.default=yi;var St={},ot={},dn={exports:{}};(function(k,f){ace.define("ace/split",["require","exports","module","ace/lib/oop","ace/lib/lang","ace/lib/event_emitter","ace/editor","ace/virtual_renderer","ace/edit_session"],function(n,l,T){var e=n("./lib/oop");n("./lib/lang");var a=n("./lib/event_emitter").EventEmitter,u=n("./editor").Editor,g=n("./virtual_renderer").VirtualRenderer,p=n("./edit_session").EditSession,s;s=function(o,d,m){this.BELOW=1,this.BESIDE=0,this.$container=o,this.$theme=d,this.$splits=0,this.$editorCSS="",this.$editors=[],this.$orientation=this.BESIDE,this.setSplits(m||1),this.$cEditor=this.$editors[0],this.on("focus",function(_){this.$cEditor=_}.bind(this))},function(){e.implement(this,a),this.$createEditor=function(){var o=document.createElement("div");o.className=this.$editorCSS,o.style.cssText="position: absolute; top:0px; bottom:0px",this.$container.appendChild(o);var d=new u(new g(o,this.$theme));return d.on("focus",function(){this._emit("focus",d)}.bind(this)),this.$editors.push(d),d.setFontSize(this.$fontSize),d},this.setSplits=function(o){var d;if(o<1)throw"The number of splits have to be > 0!";if(o!=this.$splits){if(o>this.$splits){for(;this.$splits<this.$editors.length&&this.$splits<o;)d=this.$editors[this.$splits],this.$container.appendChild(d.container),d.setFontSize(this.$fontSize),this.$splits++;for(;this.$splits<o;)this.$createEditor(),this.$splits++}else for(;this.$splits>o;)d=this.$editors[this.$splits-1],this.$container.removeChild(d.container),this.$splits--;this.resize()}},this.getSplits=function(){return this.$splits},this.getEditor=function(o){return this.$editors[o]},this.getCurrentEditor=function(){return this.$cEditor},this.focus=function(){this.$cEditor.focus()},this.blur=function(){this.$cEditor.blur()},this.setTheme=function(o){this.$editors.forEach(function(d){d.setTheme(o)})},this.setKeyboardHandler=function(o){this.$editors.forEach(function(d){d.setKeyboardHandler(o)})},this.forEach=function(o,d){this.$editors.forEach(o,d)},this.$fontSize="",this.setFontSize=function(o){this.$fontSize=o,this.forEach(function(d){d.setFontSize(o)})},this.$cloneSession=function(o){var d=new p(o.getDocument(),o.getMode()),m=o.getUndoManager();return d.setUndoManager(m),d.setTabSize(o.getTabSize()),d.setUseSoftTabs(o.getUseSoftTabs()),d.setOverwrite(o.getOverwrite()),d.setBreakpoints(o.getBreakpoints()),d.setUseWrapMode(o.getUseWrapMode()),d.setUseWorker(o.getUseWorker()),d.setWrapLimitRange(o.$wrapLimitRange.min,o.$wrapLimitRange.max),d.$foldData=o.$cloneFoldData(),d},this.setSession=function(o,d){var m;d==null?m=this.$cEditor:m=this.$editors[d];var _=this.$editors.some(function(C){return C.session===o});return _&&(o=this.$cloneSession(o)),m.setSession(o),o},this.getOrientation=function(){return this.$orientation},this.setOrientation=function(o){this.$orientation!=o&&(this.$orientation=o,this.resize())},this.resize=function(){var o=this.$container.clientWidth,d=this.$container.clientHeight,m;if(this.$orientation==this.BESIDE)for(var _=o/this.$splits,C=0;C<this.$splits;C++)m=this.$editors[C],m.container.style.width=_+"px",m.container.style.top="0px",m.container.style.left=C*_+"px",m.container.style.height=d+"px",m.resize();else for(var b=d/this.$splits,C=0;C<this.$splits;C++)m=this.$editors[C],m.container.style.width=o+"px",m.container.style.top=C*b+"px",m.container.style.left="0px",m.container.style.height=b+"px",m.resize()}}.call(s.prototype),l.Split=s}),ace.define("ace/ext/split",["require","exports","module","ace/split"],function(n,l,T){T.exports=n("../split")}),function(){ace.require(["ace/ext/split"],function(n){k&&(k.exports=n)})}()})(dn);var xi=dn.exports,wi="Expected a function",gn="__lodash_hash_undefined__",fn=1/0,_i="[object Function]",ki="[object GeneratorFunction]",$i="[object Symbol]",Si=/\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/,Ci=/^\w*$/,Ti=/^\./,Ei=/[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g,Ai=/[\\^$.*+?()[\]{}|]/g,Mi=/\\(\\)?/g,Ri=/^\[object .+?Constructor\]$/,Oi=typeof re=="object"&&re&&re.Object===Object&&re,Li=typeof self=="object"&&self&&self.Object===Object&&self,Ct=Oi||Li||Function("return this")();function Ii(k,f){return k==null?void 0:k[f]}function Pi(k){var f=!1;if(k!=null&&typeof k.toString!="function")try{f=!!(k+"")}catch(n){}return f}var Fi=Array.prototype,Ni=Function.prototype,mn=Object.prototype,vt=Ct["__core-js_shared__"],nn=function(){var k=/[^.]+$/.exec(vt&&vt.keys&&vt.keys.IE_PROTO||"");return k?"Symbol(src)_1."+k:""}(),bn=Ni.toString,Tt=mn.hasOwnProperty,vn=mn.toString,zi=RegExp("^"+bn.call(Tt).replace(Ai,"\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g,"$1.*?")+"$"),rn=Ct.Symbol,Di=Fi.splice,Bi=yn(Ct,"Map"),Ge=yn(Object,"create"),on=rn?rn.prototype:void 0,sn=on?on.toString:void 0;function Ie(k){var f=-1,n=k?k.length:0;for(this.clear();++f<n;){var l=k[f];this.set(l[0],l[1])}}function ji(){this.__data__=Ge?Ge(null):{}}function Hi(k){return this.has(k)&&delete this.__data__[k]}function qi(k){var f=this.__data__;if(Ge){var n=f[k];return n===gn?void 0:n}return Tt.call(f,k)?f[k]:void 0}function Ui(k){var f=this.__data__;return Ge?f[k]!==void 0:Tt.call(f,k)}function Wi(k,f){var n=this.__data__;return n[k]=Ge&&f===void 0?gn:f,this}Ie.prototype.clear=ji;Ie.prototype.delete=Hi;Ie.prototype.get=qi;Ie.prototype.has=Ui;Ie.prototype.set=Wi;function Be(k){var f=-1,n=k?k.length:0;for(this.clear();++f<n;){var l=k[f];this.set(l[0],l[1])}}function Vi(){this.__data__=[]}function Gi(k){var f=this.__data__,n=st(f,k);if(n<0)return!1;var l=f.length-1;return n==l?f.pop():Di.call(f,n,1),!0}function Ki(k){var f=this.__data__,n=st(f,k);return n<0?void 0:f[n][1]}function Xi(k){return st(this.__data__,k)>-1}function Yi(k,f){var n=this.__data__,l=st(n,k);return l<0?n.push([k,f]):n[l][1]=f,this}Be.prototype.clear=Vi;Be.prototype.delete=Gi;Be.prototype.get=Ki;Be.prototype.has=Xi;Be.prototype.set=Yi;function Pe(k){var f=-1,n=k?k.length:0;for(this.clear();++f<n;){var l=k[f];this.set(l[0],l[1])}}function Ji(){this.__data__={hash:new Ie,map:new(Bi||Be),string:new Ie}}function Zi(k){return at(this,k).delete(k)}function Qi(k){return at(this,k).get(k)}function eo(k){return at(this,k).has(k)}function to(k,f){return at(this,k).set(k,f),this}Pe.prototype.clear=Ji;Pe.prototype.delete=Zi;Pe.prototype.get=Qi;Pe.prototype.has=eo;Pe.prototype.set=to;function st(k,f){for(var n=k.length;n--;)if(uo(k[n][0],f))return n;return-1}function no(k,f){f=so(f,k)?[f]:oo(f);for(var n=0,l=f.length;k!=null&&n<l;)k=k[po(f[n++])];return n&&n==l?k:void 0}function ro(k){if(!wn(k)||lo(k))return!1;var f=go(k)||Pi(k)?zi:Ri;return f.test(ho(k))}function io(k){if(typeof k=="string")return k;if(At(k))return sn?sn.call(k):"";var f=k+"";return f=="0"&&1/k==-fn?"-0":f}function oo(k){return xn(k)?k:co(k)}function at(k,f){var n=k.__data__;return ao(f)?n[typeof f=="string"?"string":"hash"]:n.map}function yn(k,f){var n=Ii(k,f);return ro(n)?n:void 0}function so(k,f){if(xn(k))return!1;var n=typeof k;return n=="number"||n=="symbol"||n=="boolean"||k==null||At(k)?!0:Ci.test(k)||!Si.test(k)||f!=null&&k in Object(f)}function ao(k){var f=typeof k;return f=="string"||f=="number"||f=="symbol"||f=="boolean"?k!=="__proto__":k===null}function lo(k){return!!nn&&nn in k}var co=Et(function(k){k=mo(k);var f=[];return Ti.test(k)&&f.push(""),k.replace(Ei,function(n,l,T,e){f.push(T?e.replace(Mi,"$1"):l||n)}),f});function po(k){if(typeof k=="string"||At(k))return k;var f=k+"";return f=="0"&&1/k==-fn?"-0":f}function ho(k){if(k!=null){try{return bn.call(k)}catch(f){}try{return k+""}catch(f){}}return""}function Et(k,f){if(typeof k!="function"||f&&typeof f!="function")throw new TypeError(wi);var n=function(){var l=arguments,T=f?f.apply(this,l):l[0],e=n.cache;if(e.has(T))return e.get(T);var a=k.apply(this,l);return n.cache=e.set(T,a),a};return n.cache=new(Et.Cache||Pe),n}Et.Cache=Pe;function uo(k,f){return k===f||k!==k&&f!==f}var xn=Array.isArray;function go(k){var f=wn(k)?vn.call(k):"";return f==_i||f==ki}function wn(k){var f=typeof k;return!!k&&(f=="object"||f=="function")}function fo(k){return!!k&&typeof k=="object"}function At(k){return typeof k=="symbol"||fo(k)&&vn.call(k)==$i}function mo(k){return k==null?"":io(k)}function bo(k,f,n){var l=k==null?void 0:no(k,f);return l===void 0?n:l}var vo=bo,yo=re&&re.__extends||function(){var k=function(f,n){return k=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(l,T){l.__proto__=T}||function(l,T){for(var e in T)Object.prototype.hasOwnProperty.call(T,e)&&(l[e]=T[e])},k(f,n)};return function(f,n){if(typeof n!="function"&&n!==null)throw new TypeError("Class extends value "+String(n)+" is not a constructor or null");k(f,n);function l(){this.constructor=f}f.prototype=n===null?Object.create(n):(l.prototype=n.prototype,new l)}}(),_t=re&&re.__assign||function(){return _t=Object.assign||function(k){for(var f,n=1,l=arguments.length;n<l;n++){f=arguments[n];for(var T in f)Object.prototype.hasOwnProperty.call(f,T)&&(k[T]=f[T])}return k},_t.apply(this,arguments)};Object.defineProperty(ot,"__esModule",{value:!0});var Le=be,yt=(0,Le.getAceInstance)(),xo=rt,wo=xi,q=kt,an=he,xt=un,_e=vo,_o=function(k){yo(f,k);function f(n){var l=k.call(this,n)||this;return Le.editorEvents.forEach(function(T){l[T]=l[T].bind(l)}),l.debounce=Le.debounce,l}return f.prototype.isInShadow=function(n){for(var l=n&&n.parentNode;l;){if(l.toString()==="[object ShadowRoot]")return!0;l=l.parentNode}return!1},f.prototype.componentDidMount=function(){var n=this,l=this.props,T=l.className,e=l.onBeforeLoad,a=l.mode,u=l.focus,g=l.theme,p=l.fontSize,s=l.value,o=l.defaultValue,d=l.cursorStart,m=l.showGutter,_=l.wrapEnabled,C=l.showPrintMargin,b=l.scrollMargin,y=b===void 0?[0,0,0,0]:b,A=l.keyboardHandler,I=l.onLoad,R=l.commands,S=l.annotations,t=l.markers,i=l.splits;this.editor=yt.edit(this.refEditor),this.isInShadow(this.refEditor)&&this.editor.renderer.attachToShadowRoot(),this.editor.setTheme("ace/theme/".concat(g)),e&&e(yt);var r=Object.keys(this.props.editorProps),c=new wo.Split(this.editor.container,"ace/theme/".concat(g),i);this.editor.env.split=c,this.splitEditor=c.getEditor(0),this.split=c,this.editor.setShowPrintMargin(!1),this.editor.renderer.setShowGutter(!1);var h=this.splitEditor.$options;this.props.debounceChangePeriod&&(this.onChange=this.debounce(this.onChange,this.props.debounceChangePeriod)),c.forEach(function(w,$){for(var E=0;E<r.length;E++)w[r[E]]=n.props.editorProps[r[E]];var M=_e(o,$),L=_e(s,$,"");w.session.setUndoManager(new yt.UndoManager),w.setTheme("ace/theme/".concat(g)),w.renderer.setScrollMargin(y[0],y[1],y[2],y[3]),w.getSession().setMode("ace/mode/".concat(a)),w.setFontSize(p),w.renderer.setShowGutter(m),w.getSession().setUseWrapMode(_),w.setShowPrintMargin(C),w.on("focus",n.onFocus),w.on("blur",n.onBlur),w.on("input",n.onInput),w.on("copy",n.onCopy),w.on("paste",n.onPaste),w.on("change",n.onChange),w.getSession().selection.on("changeSelection",n.onSelectionChange),w.getSession().selection.on("changeCursor",n.onCursorChange),w.session.on("changeScrollTop",n.onScroll),w.setValue(M===void 0?L:M,d);var N=_e(S,$,[]),P=_e(t,$,[]);w.getSession().setAnnotations(N),P&&P.length>0&&n.handleMarkers(P,w);for(var E=0;E<Le.editorOptions.length;E++){var z=Le.editorOptions[E];h.hasOwnProperty(z)?w.setOption(z,n.props[z]):n.props[z]&&console.warn("ReaceAce: editor option ".concat(z," was activated but not found. Did you need to import a related tool or did you possibly mispell the option?"))}n.handleOptions(n.props,w),Array.isArray(R)&&R.forEach(function(j){typeof j.exec=="string"?w.commands.bindKey(j.bindKey,j.exec):w.commands.addCommand(j)}),A&&w.setKeyboardHandler("ace/keyboard/"+A)}),T&&(this.refEditor.className+=" "+T),u&&this.splitEditor.focus();var x=this.editor.env.split;x.setOrientation(this.props.orientation==="below"?x.BELOW:x.BESIDE),x.resize(!0),I&&I(x)},f.prototype.componentDidUpdate=function(n){var l=this,T=n,e=this.props,a=this.editor.env.split;if(e.splits!==T.splits&&a.setSplits(e.splits),e.orientation!==T.orientation&&a.setOrientation(e.orientation==="below"?a.BELOW:a.BESIDE),a.forEach(function(s,o){e.mode!==T.mode&&s.getSession().setMode("ace/mode/"+e.mode),e.keyboardHandler!==T.keyboardHandler&&(e.keyboardHandler?s.setKeyboardHandler("ace/keyboard/"+e.keyboardHandler):s.setKeyboardHandler(null)),e.fontSize!==T.fontSize&&s.setFontSize(e.fontSize),e.wrapEnabled!==T.wrapEnabled&&s.getSession().setUseWrapMode(e.wrapEnabled),e.showPrintMargin!==T.showPrintMargin&&s.setShowPrintMargin(e.showPrintMargin),e.showGutter!==T.showGutter&&s.renderer.setShowGutter(e.showGutter);for(var d=0;d<Le.editorOptions.length;d++){var m=Le.editorOptions[d];e[m]!==T[m]&&s.setOption(m,e[m])}xt(e.setOptions,T.setOptions)||l.handleOptions(e,s);var _=_e(e.value,o,"");if(s.getValue()!==_){l.silent=!0;var C=s.session.selection.toJSON();s.setValue(_,e.cursorStart),s.session.selection.fromJSON(C),l.silent=!1}var b=_e(e.annotations,o,[]),y=_e(T.annotations,o,[]);xt(b,y)||s.getSession().setAnnotations(b);var A=_e(e.markers,o,[]),I=_e(T.markers,o,[]);!xt(A,I)&&Array.isArray(A)&&l.handleMarkers(A,s)}),e.className!==T.className){var u=this.refEditor.className,g=u.trim().split(" "),p=T.className.trim().split(" ");p.forEach(function(s){var o=g.indexOf(s);g.splice(o,1)}),this.refEditor.className=" "+e.className+" "+g.join(" ")}e.theme!==T.theme&&a.setTheme("ace/theme/"+e.theme),e.focus&&!T.focus&&this.splitEditor.focus(),(e.height!==this.props.height||e.width!==this.props.width)&&this.editor.resize()},f.prototype.componentWillUnmount=function(){this.editor.destroy(),this.editor=null},f.prototype.onChange=function(n){if(this.props.onChange&&!this.silent){var l=[];this.editor.env.split.forEach(function(T){l.push(T.getValue())}),this.props.onChange(l,n)}},f.prototype.onSelectionChange=function(n){if(this.props.onSelectionChange){var l=[];this.editor.env.split.forEach(function(T){l.push(T.getSelection())}),this.props.onSelectionChange(l,n)}},f.prototype.onCursorChange=function(n){if(this.props.onCursorChange){var l=[];this.editor.env.split.forEach(function(T){l.push(T.getSelection())}),this.props.onCursorChange(l,n)}},f.prototype.onFocus=function(n){this.props.onFocus&&this.props.onFocus(n)},f.prototype.onInput=function(n){this.props.onInput&&this.props.onInput(n)},f.prototype.onBlur=function(n){this.props.onBlur&&this.props.onBlur(n)},f.prototype.onCopy=function(n){this.props.onCopy&&this.props.onCopy(n)},f.prototype.onPaste=function(n){this.props.onPaste&&this.props.onPaste(n)},f.prototype.onScroll=function(){this.props.onScroll&&this.props.onScroll(this.editor)},f.prototype.handleOptions=function(n,l){for(var T=Object.keys(n.setOptions),e=0;e<T.length;e++)l.setOption(T[e],n.setOptions[T[e]])},f.prototype.handleMarkers=function(n,l){var T=l.getSession().getMarkers(!0);for(var e in T)T.hasOwnProperty(e)&&l.getSession().removeMarker(T[e].id);T=l.getSession().getMarkers(!1);for(var e in T)T.hasOwnProperty(e)&&l.getSession().removeMarker(T[e].id);n.forEach(function(a){var u=a.startRow,g=a.startCol,p=a.endRow,s=a.endCol,o=a.className,d=a.type,m=a.inFront,_=m===void 0?!1:m,C=new xo.Range(u,g,p,s);l.getSession().addMarker(C,o,d,_)})},f.prototype.updateRef=function(n){this.refEditor=n},f.prototype.render=function(){var n=this.props,l=n.name,T=n.width,e=n.height,a=n.style,u=_t({width:T,height:e},a);return an.createElement("div",{ref:this.updateRef,id:l,style:u})},f.propTypes={className:q.string,debounceChangePeriod:q.number,defaultValue:q.arrayOf(q.string),focus:q.bool,fontSize:q.oneOfType([q.number,q.string]),height:q.string,mode:q.string,name:q.string,onBlur:q.func,onChange:q.func,onCopy:q.func,onFocus:q.func,onInput:q.func,onLoad:q.func,onPaste:q.func,onScroll:q.func,orientation:q.string,showGutter:q.bool,splits:q.number,theme:q.string,value:q.arrayOf(q.string),width:q.string,onSelectionChange:q.func,onCursorChange:q.func,onBeforeLoad:q.func,minLines:q.number,maxLines:q.number,readOnly:q.bool,highlightActiveLine:q.bool,tabSize:q.number,showPrintMargin:q.bool,cursorStart:q.number,editorProps:q.object,setOptions:q.object,style:q.object,scrollMargin:q.array,annotations:q.array,markers:q.array,keyboardHandler:q.string,wrapEnabled:q.bool,enableBasicAutocompletion:q.oneOfType([q.bool,q.array]),enableLiveAutocompletion:q.oneOfType([q.bool,q.array]),commands:q.array},f.defaultProps={name:"ace-editor",focus:!1,orientation:"beside",splits:2,mode:"",theme:"",height:"500px",width:"500px",value:[],fontSize:12,showGutter:!0,onChange:null,onPaste:null,onLoad:null,onScroll:null,minLines:null,maxLines:null,readOnly:!1,highlightActiveLine:!0,showPrintMargin:!0,tabSize:4,cursorStart:1,editorProps:{},style:{},scrollMargin:[0,0,0,0],setOptions:{},wrapEnabled:!1,enableBasicAutocompletion:!1,enableLiveAutocompletion:!1},f}(an.Component);ot.default=_o;var _n={exports:{}};(function(k){var f=function(){this.Diff_Timeout=1,this.Diff_EditCost=4,this.Match_Threshold=.5,this.Match_Distance=1e3,this.Patch_DeleteThreshold=.5,this.Patch_Margin=4,this.Match_MaxBits=32},n=-1,l=1,T=0;f.Diff=function(e,a){return[e,a]},f.prototype.diff_main=function(e,a,u,g){typeof g=="undefined"&&(this.Diff_Timeout<=0?g=Number.MAX_VALUE:g=new Date().getTime()+this.Diff_Timeout*1e3);var p=g;if(e==null||a==null)throw new Error("Null input. (diff_main)");if(e==a)return e?[new f.Diff(T,e)]:[];typeof u=="undefined"&&(u=!0);var s=u,o=this.diff_commonPrefix(e,a),d=e.substring(0,o);e=e.substring(o),a=a.substring(o),o=this.diff_commonSuffix(e,a);var m=e.substring(e.length-o);e=e.substring(0,e.length-o),a=a.substring(0,a.length-o);var _=this.diff_compute_(e,a,s,p);return d&&_.unshift(new f.Diff(T,d)),m&&_.push(new f.Diff(T,m)),this.diff_cleanupMerge(_),_},f.prototype.diff_compute_=function(e,a,u,g){var p;if(!e)return[new f.Diff(l,a)];if(!a)return[new f.Diff(n,e)];var s=e.length>a.length?e:a,o=e.length>a.length?a:e,d=s.indexOf(o);if(d!=-1)return p=[new f.Diff(l,s.substring(0,d)),new f.Diff(T,o),new f.Diff(l,s.substring(d+o.length))],e.length>a.length&&(p[0][0]=p[2][0]=n),p;if(o.length==1)return[new f.Diff(n,e),new f.Diff(l,a)];var m=this.diff_halfMatch_(e,a);if(m){var _=m[0],C=m[1],b=m[2],y=m[3],A=m[4],I=this.diff_main(_,b,u,g),R=this.diff_main(C,y,u,g);return I.concat([new f.Diff(T,A)],R)}return u&&e.length>100&&a.length>100?this.diff_lineMode_(e,a,g):this.diff_bisect_(e,a,g)},f.prototype.diff_lineMode_=function(e,a,u){var g=this.diff_linesToChars_(e,a);e=g.chars1,a=g.chars2;var p=g.lineArray,s=this.diff_main(e,a,!1,u);this.diff_charsToLines_(s,p),this.diff_cleanupSemantic(s),s.push(new f.Diff(T,""));for(var o=0,d=0,m=0,_="",C="";o<s.length;){switch(s[o][0]){case l:m++,C+=s[o][1];break;case n:d++,_+=s[o][1];break;case T:if(d>=1&&m>=1){s.splice(o-d-m,d+m),o=o-d-m;for(var b=this.diff_main(_,C,!1,u),y=b.length-1;y>=0;y--)s.splice(o,0,b[y]);o=o+b.length}m=0,d=0,_="",C="";break}o++}return s.pop(),s},f.prototype.diff_bisect_=function(e,a,u){for(var g=e.length,p=a.length,s=Math.ceil((g+p)/2),o=s,d=2*s,m=new Array(d),_=new Array(d),C=0;C<d;C++)m[C]=-1,_[C]=-1;m[o+1]=0,_[o+1]=0;for(var b=g-p,y=b%2!=0,A=0,I=0,R=0,S=0,t=0;t<s&&!(new Date().getTime()>u);t++){for(var i=-t+A;i<=t-I;i+=2){var r=o+i,c;i==-t||i!=t&&m[r-1]<m[r+1]?c=m[r+1]:c=m[r-1]+1;for(var h=c-i;c<g&&h<p&&e.charAt(c)==a.charAt(h);)c++,h++;if(m[r]=c,c>g)I+=2;else if(h>p)A+=2;else if(y){var x=o+b-i;if(x>=0&&x<d&&_[x]!=-1){var w=g-_[x];if(c>=w)return this.diff_bisectSplit_(e,a,c,h,u)}}}for(var $=-t+R;$<=t-S;$+=2){var x=o+$,w;$==-t||$!=t&&_[x-1]<_[x+1]?w=_[x+1]:w=_[x-1]+1;for(var E=w-$;w<g&&E<p&&e.charAt(g-w-1)==a.charAt(p-E-1);)w++,E++;if(_[x]=w,w>g)S+=2;else if(E>p)R+=2;else if(!y){var r=o+b-$;if(r>=0&&r<d&&m[r]!=-1){var c=m[r],h=o+c-r;if(w=g-w,c>=w)return this.diff_bisectSplit_(e,a,c,h,u)}}}}return[new f.Diff(n,e),new f.Diff(l,a)]},f.prototype.diff_bisectSplit_=function(e,a,u,g,p){var s=e.substring(0,u),o=a.substring(0,g),d=e.substring(u),m=a.substring(g),_=this.diff_main(s,o,!1,p),C=this.diff_main(d,m,!1,p);return _.concat(C)},f.prototype.diff_linesToChars_=function(e,a){var u=[],g={};u[0]="";function p(m){for(var _="",C=0,b=-1,y=u.length;b<m.length-1;){b=m.indexOf(`
`,C),b==-1&&(b=m.length-1);var A=m.substring(C,b+1);(g.hasOwnProperty?g.hasOwnProperty(A):g[A]!==void 0)?_+=String.fromCharCode(g[A]):(y==s&&(A=m.substring(C),b=m.length),_+=String.fromCharCode(y),g[A]=y,u[y++]=A),C=b+1}return _}var s=4e4,o=p(e);s=65535;var d=p(a);return{chars1:o,chars2:d,lineArray:u}},f.prototype.diff_charsToLines_=function(e,a){for(var u=0;u<e.length;u++){for(var g=e[u][1],p=[],s=0;s<g.length;s++)p[s]=a[g.charCodeAt(s)];e[u][1]=p.join("")}},f.prototype.diff_commonPrefix=function(e,a){if(!e||!a||e.charAt(0)!=a.charAt(0))return 0;for(var u=0,g=Math.min(e.length,a.length),p=g,s=0;u<p;)e.substring(s,p)==a.substring(s,p)?(u=p,s=u):g=p,p=Math.floor((g-u)/2+u);return p},f.prototype.diff_commonSuffix=function(e,a){if(!e||!a||e.charAt(e.length-1)!=a.charAt(a.length-1))return 0;for(var u=0,g=Math.min(e.length,a.length),p=g,s=0;u<p;)e.substring(e.length-p,e.length-s)==a.substring(a.length-p,a.length-s)?(u=p,s=u):g=p,p=Math.floor((g-u)/2+u);return p},f.prototype.diff_commonOverlap_=function(e,a){var u=e.length,g=a.length;if(u==0||g==0)return 0;u>g?e=e.substring(u-g):u<g&&(a=a.substring(0,u));var p=Math.min(u,g);if(e==a)return p;for(var s=0,o=1;;){var d=e.substring(p-o),m=a.indexOf(d);if(m==-1)return s;o+=m,(m==0||e.substring(p-o)==a.substring(0,o))&&(s=o,o++)}},f.prototype.diff_halfMatch_=function(e,a){if(this.Diff_Timeout<=0)return null;var u=e.length>a.length?e:a,g=e.length>a.length?a:e;if(u.length<4||g.length*2<u.length)return null;var p=this;function s(I,R,S){for(var t=I.substring(S,S+Math.floor(I.length/4)),i=-1,r="",c,h,x,w;(i=R.indexOf(t,i+1))!=-1;){var $=p.diff_commonPrefix(I.substring(S),R.substring(i)),E=p.diff_commonSuffix(I.substring(0,S),R.substring(0,i));r.length<E+$&&(r=R.substring(i-E,i)+R.substring(i,i+$),c=I.substring(0,S-E),h=I.substring(S+$),x=R.substring(0,i-E),w=R.substring(i+$))}return r.length*2>=I.length?[c,h,x,w,r]:null}var o=s(u,g,Math.ceil(u.length/4)),d=s(u,g,Math.ceil(u.length/2)),m;if(!o&&!d)return null;d?o?m=o[4].length>d[4].length?o:d:m=d:m=o;var _,C,b,y;e.length>a.length?(_=m[0],C=m[1],b=m[2],y=m[3]):(b=m[0],y=m[1],_=m[2],C=m[3]);var A=m[4];return[_,C,b,y,A]},f.prototype.diff_cleanupSemantic=function(e){for(var a=!1,u=[],g=0,p=null,s=0,o=0,d=0,m=0,_=0;s<e.length;)e[s][0]==T?(u[g++]=s,o=m,d=_,m=0,_=0,p=e[s][1]):(e[s][0]==l?m+=e[s][1].length:_+=e[s][1].length,p&&p.length<=Math.max(o,d)&&p.length<=Math.max(m,_)&&(e.splice(u[g-1],0,new f.Diff(n,p)),e[u[g-1]+1][0]=l,g--,g--,s=g>0?u[g-1]:-1,o=0,d=0,m=0,_=0,p=null,a=!0)),s++;for(a&&this.diff_cleanupMerge(e),this.diff_cleanupSemanticLossless(e),s=1;s<e.length;){if(e[s-1][0]==n&&e[s][0]==l){var C=e[s-1][1],b=e[s][1],y=this.diff_commonOverlap_(C,b),A=this.diff_commonOverlap_(b,C);y>=A?(y>=C.length/2||y>=b.length/2)&&(e.splice(s,0,new f.Diff(T,b.substring(0,y))),e[s-1][1]=C.substring(0,C.length-y),e[s+1][1]=b.substring(y),s++):(A>=C.length/2||A>=b.length/2)&&(e.splice(s,0,new f.Diff(T,C.substring(0,A))),e[s-1][0]=l,e[s-1][1]=b.substring(0,b.length-A),e[s+1][0]=n,e[s+1][1]=C.substring(A),s++),s++}s++}},f.prototype.diff_cleanupSemanticLossless=function(e){function a(A,I){if(!A||!I)return 6;var R=A.charAt(A.length-1),S=I.charAt(0),t=R.match(f.nonAlphaNumericRegex_),i=S.match(f.nonAlphaNumericRegex_),r=t&&R.match(f.whitespaceRegex_),c=i&&S.match(f.whitespaceRegex_),h=r&&R.match(f.linebreakRegex_),x=c&&S.match(f.linebreakRegex_),w=h&&A.match(f.blanklineEndRegex_),$=x&&I.match(f.blanklineStartRegex_);return w||$?5:h||x?4:t&&!r&&c?3:r||c?2:t||i?1:0}for(var u=1;u<e.length-1;){if(e[u-1][0]==T&&e[u+1][0]==T){var g=e[u-1][1],p=e[u][1],s=e[u+1][1],o=this.diff_commonSuffix(g,p);if(o){var d=p.substring(p.length-o);g=g.substring(0,g.length-o),p=d+p.substring(0,p.length-o),s=d+s}for(var m=g,_=p,C=s,b=a(g,p)+a(p,s);p.charAt(0)===s.charAt(0);){g+=p.charAt(0),p=p.substring(1)+s.charAt(0),s=s.substring(1);var y=a(g,p)+a(p,s);y>=b&&(b=y,m=g,_=p,C=s)}e[u-1][1]!=m&&(m?e[u-1][1]=m:(e.splice(u-1,1),u--),e[u][1]=_,C?e[u+1][1]=C:(e.splice(u+1,1),u--))}u++}},f.nonAlphaNumericRegex_=/[^a-zA-Z0-9]/,f.whitespaceRegex_=/\s/,f.linebreakRegex_=/[\r\n]/,f.blanklineEndRegex_=/\n\r?\n$/,f.blanklineStartRegex_=/^\r?\n\r?\n/,f.prototype.diff_cleanupEfficiency=function(e){for(var a=!1,u=[],g=0,p=null,s=0,o=!1,d=!1,m=!1,_=!1;s<e.length;)e[s][0]==T?(e[s][1].length<this.Diff_EditCost&&(m||_)?(u[g++]=s,o=m,d=_,p=e[s][1]):(g=0,p=null),m=_=!1):(e[s][0]==n?_=!0:m=!0,p&&(o&&d&&m&&_||p.length<this.Diff_EditCost/2&&o+d+m+_==3)&&(e.splice(u[g-1],0,new f.Diff(n,p)),e[u[g-1]+1][0]=l,g--,p=null,o&&d?(m=_=!0,g=0):(g--,s=g>0?u[g-1]:-1,m=_=!1),a=!0)),s++;a&&this.diff_cleanupMerge(e)},f.prototype.diff_cleanupMerge=function(e){e.push(new f.Diff(T,""));for(var a=0,u=0,g=0,p="",s="",o;a<e.length;)switch(e[a][0]){case l:g++,s+=e[a][1],a++;break;case n:u++,p+=e[a][1],a++;break;case T:u+g>1?(u!==0&&g!==0&&(o=this.diff_commonPrefix(s,p),o!==0&&(a-u-g>0&&e[a-u-g-1][0]==T?e[a-u-g-1][1]+=s.substring(0,o):(e.splice(0,0,new f.Diff(T,s.substring(0,o))),a++),s=s.substring(o),p=p.substring(o)),o=this.diff_commonSuffix(s,p),o!==0&&(e[a][1]=s.substring(s.length-o)+e[a][1],s=s.substring(0,s.length-o),p=p.substring(0,p.length-o))),a-=u+g,e.splice(a,u+g),p.length&&(e.splice(a,0,new f.Diff(n,p)),a++),s.length&&(e.splice(a,0,new f.Diff(l,s)),a++),a++):a!==0&&e[a-1][0]==T?(e[a-1][1]+=e[a][1],e.splice(a,1)):a++,g=0,u=0,p="",s="";break}e[e.length-1][1]===""&&e.pop();var d=!1;for(a=1;a<e.length-1;)e[a-1][0]==T&&e[a+1][0]==T&&(e[a][1].substring(e[a][1].length-e[a-1][1].length)==e[a-1][1]?(e[a][1]=e[a-1][1]+e[a][1].substring(0,e[a][1].length-e[a-1][1].length),e[a+1][1]=e[a-1][1]+e[a+1][1],e.splice(a-1,1),d=!0):e[a][1].substring(0,e[a+1][1].length)==e[a+1][1]&&(e[a-1][1]+=e[a+1][1],e[a][1]=e[a][1].substring(e[a+1][1].length)+e[a+1][1],e.splice(a+1,1),d=!0)),a++;d&&this.diff_cleanupMerge(e)},f.prototype.diff_xIndex=function(e,a){var u=0,g=0,p=0,s=0,o;for(o=0;o<e.length&&(e[o][0]!==l&&(u+=e[o][1].length),e[o][0]!==n&&(g+=e[o][1].length),!(u>a));o++)p=u,s=g;return e.length!=o&&e[o][0]===n?s:s+(a-p)},f.prototype.diff_prettyHtml=function(e){for(var a=[],u=/&/g,g=/</g,p=/>/g,s=/\n/g,o=0;o<e.length;o++){var d=e[o][0],m=e[o][1],_=m.replace(u,"&amp;").replace(g,"&lt;").replace(p,"&gt;").replace(s,"&para;<br>");switch(d){case l:a[o]='<ins style="background:#e6ffe6;">'+_+"</ins>";break;case n:a[o]='<del style="background:#ffe6e6;">'+_+"</del>";break;case T:a[o]="<span>"+_+"</span>";break}}return a.join("")},f.prototype.diff_text1=function(e){for(var a=[],u=0;u<e.length;u++)e[u][0]!==l&&(a[u]=e[u][1]);return a.join("")},f.prototype.diff_text2=function(e){for(var a=[],u=0;u<e.length;u++)e[u][0]!==n&&(a[u]=e[u][1]);return a.join("")},f.prototype.diff_levenshtein=function(e){for(var a=0,u=0,g=0,p=0;p<e.length;p++){var s=e[p][0],o=e[p][1];switch(s){case l:u+=o.length;break;case n:g+=o.length;break;case T:a+=Math.max(u,g),u=0,g=0;break}}return a+=Math.max(u,g),a},f.prototype.diff_toDelta=function(e){for(var a=[],u=0;u<e.length;u++)switch(e[u][0]){case l:a[u]="+"+encodeURI(e[u][1]);break;case n:a[u]="-"+e[u][1].length;break;case T:a[u]="="+e[u][1].length;break}return a.join("	").replace(/%20/g," ")},f.prototype.diff_fromDelta=function(e,a){for(var u=[],g=0,p=0,s=a.split(/\t/g),o=0;o<s.length;o++){var d=s[o].substring(1);switch(s[o].charAt(0)){case"+":try{u[g++]=new f.Diff(l,decodeURI(d))}catch(C){throw new Error("Illegal escape in diff_fromDelta: "+d)}break;case"-":case"=":var m=parseInt(d,10);if(isNaN(m)||m<0)throw new Error("Invalid number in diff_fromDelta: "+d);var _=e.substring(p,p+=m);s[o].charAt(0)=="="?u[g++]=new f.Diff(T,_):u[g++]=new f.Diff(n,_);break;default:if(s[o])throw new Error("Invalid diff operation in diff_fromDelta: "+s[o])}}if(p!=e.length)throw new Error("Delta length ("+p+") does not equal source text length ("+e.length+").");return u},f.prototype.match_main=function(e,a,u){if(e==null||a==null||u==null)throw new Error("Null input. (match_main)");return u=Math.max(0,Math.min(u,e.length)),e==a?0:e.length?e.substring(u,u+a.length)==a?u:this.match_bitap_(e,a,u):-1},f.prototype.match_bitap_=function(e,a,u){if(a.length>this.Match_MaxBits)throw new Error("Pattern too long for this browser.");var g=this.match_alphabet_(a),p=this;function s(c,h){var x=c/a.length,w=Math.abs(u-h);return p.Match_Distance?x+w/p.Match_Distance:w?1:x}var o=this.Match_Threshold,d=e.indexOf(a,u);d!=-1&&(o=Math.min(s(0,d),o),d=e.lastIndexOf(a,u+a.length),d!=-1&&(o=Math.min(s(0,d),o)));var m=1<<a.length-1;d=-1;for(var _,C,b=a.length+e.length,y,A=0;A<a.length;A++){for(_=0,C=b;_<C;)s(A,u+C)<=o?_=C:b=C,C=Math.floor((b-_)/2+_);b=C;var I=Math.max(1,u-C+1),R=Math.min(u+C,e.length)+a.length,S=Array(R+2);S[R+1]=(1<<A)-1;for(var t=R;t>=I;t--){var i=g[e.charAt(t-1)];if(A===0?S[t]=(S[t+1]<<1|1)&i:S[t]=(S[t+1]<<1|1)&i|((y[t+1]|y[t])<<1|1)|y[t+1],S[t]&m){var r=s(A,t-1);if(r<=o)if(o=r,d=t-1,d>u)I=Math.max(1,2*u-d);else break}}if(s(A+1,u)>o)break;y=S}return d},f.prototype.match_alphabet_=function(e){for(var a={},u=0;u<e.length;u++)a[e.charAt(u)]=0;for(var u=0;u<e.length;u++)a[e.charAt(u)]|=1<<e.length-u-1;return a},f.prototype.patch_addContext_=function(e,a){if(a.length!=0){if(e.start2===null)throw Error("patch not initialized");for(var u=a.substring(e.start2,e.start2+e.length1),g=0;a.indexOf(u)!=a.lastIndexOf(u)&&u.length<this.Match_MaxBits-this.Patch_Margin-this.Patch_Margin;)g+=this.Patch_Margin,u=a.substring(e.start2-g,e.start2+e.length1+g);g+=this.Patch_Margin;var p=a.substring(e.start2-g,e.start2);p&&e.diffs.unshift(new f.Diff(T,p));var s=a.substring(e.start2+e.length1,e.start2+e.length1+g);s&&e.diffs.push(new f.Diff(T,s)),e.start1-=p.length,e.start2-=p.length,e.length1+=p.length+s.length,e.length2+=p.length+s.length}},f.prototype.patch_make=function(e,a,u){var g,p;if(typeof e=="string"&&typeof a=="string"&&typeof u=="undefined")g=e,p=this.diff_main(g,a,!0),p.length>2&&(this.diff_cleanupSemantic(p),this.diff_cleanupEfficiency(p));else if(e&&typeof e=="object"&&typeof a=="undefined"&&typeof u=="undefined")p=e,g=this.diff_text1(p);else if(typeof e=="string"&&a&&typeof a=="object"&&typeof u=="undefined")g=e,p=a;else if(typeof e=="string"&&typeof a=="string"&&u&&typeof u=="object")g=e,p=u;else throw new Error("Unknown call format to patch_make.");if(p.length===0)return[];for(var s=[],o=new f.patch_obj,d=0,m=0,_=0,C=g,b=g,y=0;y<p.length;y++){var A=p[y][0],I=p[y][1];switch(!d&&A!==T&&(o.start1=m,o.start2=_),A){case l:o.diffs[d++]=p[y],o.length2+=I.length,b=b.substring(0,_)+I+b.substring(_);break;case n:o.length1+=I.length,o.diffs[d++]=p[y],b=b.substring(0,_)+b.substring(_+I.length);break;case T:I.length<=2*this.Patch_Margin&&d&&p.length!=y+1?(o.diffs[d++]=p[y],o.length1+=I.length,o.length2+=I.length):I.length>=2*this.Patch_Margin&&d&&(this.patch_addContext_(o,C),s.push(o),o=new f.patch_obj,d=0,C=b,m=_);break}A!==l&&(m+=I.length),A!==n&&(_+=I.length)}return d&&(this.patch_addContext_(o,C),s.push(o)),s},f.prototype.patch_deepCopy=function(e){for(var a=[],u=0;u<e.length;u++){var g=e[u],p=new f.patch_obj;p.diffs=[];for(var s=0;s<g.diffs.length;s++)p.diffs[s]=new f.Diff(g.diffs[s][0],g.diffs[s][1]);p.start1=g.start1,p.start2=g.start2,p.length1=g.length1,p.length2=g.length2,a[u]=p}return a},f.prototype.patch_apply=function(e,a){if(e.length==0)return[a,[]];e=this.patch_deepCopy(e);var u=this.patch_addPadding(e);a=u+a+u,this.patch_splitMax(e);for(var g=0,p=[],s=0;s<e.length;s++){var o=e[s].start2+g,d=this.diff_text1(e[s].diffs),m,_=-1;if(d.length>this.Match_MaxBits?(m=this.match_main(a,d.substring(0,this.Match_MaxBits),o),m!=-1&&(_=this.match_main(a,d.substring(d.length-this.Match_MaxBits),o+d.length-this.Match_MaxBits),(_==-1||m>=_)&&(m=-1))):m=this.match_main(a,d,o),m==-1)p[s]=!1,g-=e[s].length2-e[s].length1;else{p[s]=!0,g=m-o;var C;if(_==-1?C=a.substring(m,m+d.length):C=a.substring(m,_+this.Match_MaxBits),d==C)a=a.substring(0,m)+this.diff_text2(e[s].diffs)+a.substring(m+d.length);else{var b=this.diff_main(d,C,!1);if(d.length>this.Match_MaxBits&&this.diff_levenshtein(b)/d.length>this.Patch_DeleteThreshold)p[s]=!1;else{this.diff_cleanupSemanticLossless(b);for(var y=0,A,I=0;I<e[s].diffs.length;I++){var R=e[s].diffs[I];R[0]!==T&&(A=this.diff_xIndex(b,y)),R[0]===l?a=a.substring(0,m+A)+R[1]+a.substring(m+A):R[0]===n&&(a=a.substring(0,m+A)+a.substring(m+this.diff_xIndex(b,y+R[1].length))),R[0]!==n&&(y+=R[1].length)}}}}}return a=a.substring(u.length,a.length-u.length),[a,p]},f.prototype.patch_addPadding=function(e){for(var a=this.Patch_Margin,u="",g=1;g<=a;g++)u+=String.fromCharCode(g);for(var g=0;g<e.length;g++)e[g].start1+=a,e[g].start2+=a;var p=e[0],s=p.diffs;if(s.length==0||s[0][0]!=T)s.unshift(new f.Diff(T,u)),p.start1-=a,p.start2-=a,p.length1+=a,p.length2+=a;else if(a>s[0][1].length){var o=a-s[0][1].length;s[0][1]=u.substring(s[0][1].length)+s[0][1],p.start1-=o,p.start2-=o,p.length1+=o,p.length2+=o}if(p=e[e.length-1],s=p.diffs,s.length==0||s[s.length-1][0]!=T)s.push(new f.Diff(T,u)),p.length1+=a,p.length2+=a;else if(a>s[s.length-1][1].length){var o=a-s[s.length-1][1].length;s[s.length-1][1]+=u.substring(0,o),p.length1+=o,p.length2+=o}return u},f.prototype.patch_splitMax=function(e){for(var a=this.Match_MaxBits,u=0;u<e.length;u++)if(!(e[u].length1<=a)){var g=e[u];e.splice(u--,1);for(var p=g.start1,s=g.start2,o="";g.diffs.length!==0;){var d=new f.patch_obj,m=!0;for(d.start1=p-o.length,d.start2=s-o.length,o!==""&&(d.length1=d.length2=o.length,d.diffs.push(new f.Diff(T,o)));g.diffs.length!==0&&d.length1<a-this.Patch_Margin;){var _=g.diffs[0][0],C=g.diffs[0][1];_===l?(d.length2+=C.length,s+=C.length,d.diffs.push(g.diffs.shift()),m=!1):_===n&&d.diffs.length==1&&d.diffs[0][0]==T&&C.length>2*a?(d.length1+=C.length,p+=C.length,m=!1,d.diffs.push(new f.Diff(_,C)),g.diffs.shift()):(C=C.substring(0,a-d.length1-this.Patch_Margin),d.length1+=C.length,p+=C.length,_===T?(d.length2+=C.length,s+=C.length):m=!1,d.diffs.push(new f.Diff(_,C)),C==g.diffs[0][1]?g.diffs.shift():g.diffs[0][1]=g.diffs[0][1].substring(C.length))}o=this.diff_text2(d.diffs),o=o.substring(o.length-this.Patch_Margin);var b=this.diff_text1(g.diffs).substring(0,this.Patch_Margin);b!==""&&(d.length1+=b.length,d.length2+=b.length,d.diffs.length!==0&&d.diffs[d.diffs.length-1][0]===T?d.diffs[d.diffs.length-1][1]+=b:d.diffs.push(new f.Diff(T,b))),m||e.splice(++u,0,d)}}},f.prototype.patch_toText=function(e){for(var a=[],u=0;u<e.length;u++)a[u]=e[u];return a.join("")},f.prototype.patch_fromText=function(e){var a=[];if(!e)return a;for(var u=e.split(`
`),g=0,p=/^@@ -(\d+),?(\d*) \+(\d+),?(\d*) @@$/;g<u.length;){var s=u[g].match(p);if(!s)throw new Error("Invalid patch string: "+u[g]);var o=new f.patch_obj;for(a.push(o),o.start1=parseInt(s[1],10),s[2]===""?(o.start1--,o.length1=1):s[2]=="0"?o.length1=0:(o.start1--,o.length1=parseInt(s[2],10)),o.start2=parseInt(s[3],10),s[4]===""?(o.start2--,o.length2=1):s[4]=="0"?o.length2=0:(o.start2--,o.length2=parseInt(s[4],10)),g++;g<u.length;){var d=u[g].charAt(0);try{var m=decodeURI(u[g].substring(1))}catch(_){throw new Error("Illegal escape in patch_fromText: "+m)}if(d=="-")o.diffs.push(new f.Diff(n,m));else if(d=="+")o.diffs.push(new f.Diff(l,m));else if(d==" ")o.diffs.push(new f.Diff(T,m));else{if(d=="@")break;if(d!=="")throw new Error('Invalid patch mode "'+d+'" in: '+m)}g++}}return a},f.patch_obj=function(){this.diffs=[],this.start1=null,this.start2=null,this.length1=0,this.length2=0},f.patch_obj.prototype.toString=function(){var e,a;this.length1===0?e=this.start1+",0":this.length1==1?e=this.start1+1:e=this.start1+1+","+this.length1,this.length2===0?a=this.start2+",0":this.length2==1?a=this.start2+1:a=this.start2+1+","+this.length2;for(var u=["@@ -"+e+" +"+a+` @@
`],g,p=0;p<this.diffs.length;p++){switch(this.diffs[p][0]){case l:g="+";break;case n:g="-";break;case T:g=" ";break}u[p+1]=g+encodeURI(this.diffs[p][1])+`
`}return u.join("").replace(/%20/g," ")},k.exports=f,k.exports.diff_match_patch=f,k.exports.DIFF_DELETE=n,k.exports.DIFF_INSERT=l,k.exports.DIFF_EQUAL=T})(_n);var ko=_n.exports,$o=re&&re.__extends||function(){var k=function(f,n){return k=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(l,T){l.__proto__=T}||function(l,T){for(var e in T)Object.prototype.hasOwnProperty.call(T,e)&&(l[e]=T[e])},k(f,n)};return function(f,n){if(typeof n!="function"&&n!==null)throw new TypeError("Class extends value "+String(n)+" is not a constructor or null");k(f,n);function l(){this.constructor=f}f.prototype=n===null?Object.create(n):(l.prototype=n.prototype,new l)}}();Object.defineProperty(St,"__esModule",{value:!0});var Y=kt,ln=he,So=ot,Co=ko,To=function(k){$o(f,k);function f(n){var l=k.call(this,n)||this;return l.state={value:l.props.value},l.onChange=l.onChange.bind(l),l.diff=l.diff.bind(l),l}return f.prototype.componentDidUpdate=function(){var n=this.props.value;n!==this.state.value&&this.setState({value:n})},f.prototype.onChange=function(n){this.setState({value:n}),this.props.onChange&&this.props.onChange(n)},f.prototype.diff=function(){var n=new Co,l=this.state.value[0],T=this.state.value[1];if(l.length===0&&T.length===0)return[];var e=n.diff_main(l,T);n.diff_cleanupSemantic(e);var a=this.generateDiffedLines(e),u=this.setCodeMarkers(a);return u},f.prototype.generateDiffedLines=function(n){var l={DIFF_EQUAL:0,DIFF_DELETE:-1,DIFF_INSERT:1},T={left:[],right:[]},e={left:1,right:1};return n.forEach(function(a){var u=a[0],g=a[1],p=g.split(`
`).length-1;if(g.length!==0){var s=g[0],o=g[g.length-1],d=0;switch(u){case l.DIFF_EQUAL:e.left+=p,e.right+=p;break;case l.DIFF_DELETE:s===`
`&&(e.left++,p--),d=p,d===0&&T.right.push({startLine:e.right,endLine:e.right}),o===`
`&&(d-=1),T.left.push({startLine:e.left,endLine:e.left+d}),e.left+=p;break;case l.DIFF_INSERT:s===`
`&&(e.right++,p--),d=p,d===0&&T.left.push({startLine:e.left,endLine:e.left}),o===`
`&&(d-=1),T.right.push({startLine:e.right,endLine:e.right+d}),e.right+=p;break;default:throw new Error("Diff type was not defined.")}}}),T},f.prototype.setCodeMarkers=function(n){n===void 0&&(n={left:[],right:[]});for(var l=[],T={left:[],right:[]},e=0;e<n.left.length;e++){var a={startRow:n.left[e].startLine-1,endRow:n.left[e].endLine,type:"text",className:"codeMarker"};T.left.push(a)}for(var e=0;e<n.right.length;e++){var a={startRow:n.right[e].startLine-1,endRow:n.right[e].endLine,type:"text",className:"codeMarker"};T.right.push(a)}return l[0]=T.left,l[1]=T.right,l},f.prototype.render=function(){var n=this.diff();return ln.createElement(So.default,{name:this.props.name,className:this.props.className,focus:this.props.focus,orientation:this.props.orientation,splits:this.props.splits,mode:this.props.mode,theme:this.props.theme,height:this.props.height,width:this.props.width,fontSize:this.props.fontSize,showGutter:this.props.showGutter,onChange:this.onChange,onPaste:this.props.onPaste,onLoad:this.props.onLoad,onScroll:this.props.onScroll,minLines:this.props.minLines,maxLines:this.props.maxLines,readOnly:this.props.readOnly,highlightActiveLine:this.props.highlightActiveLine,showPrintMargin:this.props.showPrintMargin,tabSize:this.props.tabSize,cursorStart:this.props.cursorStart,editorProps:this.props.editorProps,style:this.props.style,scrollMargin:this.props.scrollMargin,setOptions:this.props.setOptions,wrapEnabled:this.props.wrapEnabled,enableBasicAutocompletion:this.props.enableBasicAutocompletion,enableLiveAutocompletion:this.props.enableLiveAutocompletion,value:this.state.value,markers:n})},f.propTypes={cursorStart:Y.number,editorProps:Y.object,enableBasicAutocompletion:Y.bool,enableLiveAutocompletion:Y.bool,focus:Y.bool,fontSize:Y.number,height:Y.string,highlightActiveLine:Y.bool,maxLines:Y.number,minLines:Y.number,mode:Y.string,name:Y.string,className:Y.string,onLoad:Y.func,onPaste:Y.func,onScroll:Y.func,onChange:Y.func,orientation:Y.string,readOnly:Y.bool,scrollMargin:Y.array,setOptions:Y.object,showGutter:Y.bool,showPrintMargin:Y.bool,splits:Y.number,style:Y.object,tabSize:Y.number,theme:Y.string,value:Y.array,width:Y.string,wrapEnabled:Y.bool},f.defaultProps={cursorStart:1,editorProps:{},enableBasicAutocompletion:!1,enableLiveAutocompletion:!1,focus:!1,fontSize:12,height:"500px",highlightActiveLine:!0,maxLines:null,minLines:null,mode:"",name:"ace-editor",onLoad:null,onScroll:null,onPaste:null,onChange:null,orientation:"beside",readOnly:!1,scrollMargin:[0,0,0,0],setOptions:{},showGutter:!0,showPrintMargin:!0,splits:2,style:{},tabSize:4,theme:"github",value:["",""],width:"500px",wrapEnabled:!0},f}(ln.Component);St.default=To;Object.defineProperty(De,"__esModule",{value:!0});De.diff=De.split=void 0;var Eo=$t,Ao=St;De.diff=Ao.default;var Mo=ot;De.split=Mo.default;var cn=De.default=Eo.default;const Ro=Dr(Br),Oo=k=>k.lbl||k.adminLbl||k.txt,kn=()=>Object.entries(Ro).map(([k,f])=>({lbl:Oo(f)||k,val:`${k}`})),Lo=()=>Qr.map(({name:k,label:f})=>({lbl:f,val:`bfVars["${k}"]`})),Io=k=>`/* On Field ${pn(k)}*/
document.querySelector(\`#form-\${bfContentId}\`).querySelector(\`#fieldKey-\${bfSlNo}\`).addEventListener('${k}', event => {
  /* Write your code here*/
})`,le=k=>({lbl:`On ${pn(k)}`,val:Io(k)}),Po=[{type:"group-opts",name:"Global Variables",childs:[{lbl:"Form ID",val:"bf_globals[bfContentId].formId"},...Lo()]},{type:"group-opts",name:"Field Keys",childs:[...kn()]},{type:"group-opts",name:"Form Events",childs:[{lbl:"On Form Submit Success",val:"/* On Form Submit Success */\ndocument.querySelector(`#form-${bfContentId}`).addEventListener('bf-form-submit-success', ({detail:{formId, entryId, formData}}) => {\n	/* Write your code here... */\n})"},{lbl:"On Form Submit Error",val:"/* On Form Submit Error */\ndocument.querySelector(`#form-${bfContentId}`).addEventListener('bf-form-submit-error', ({detail:{formId, errors}}) => {\n	/* Write your code here... */\n})"},{lbl:"On Form Reset",val:"/* On Form Reset */\ndocument.querySelector(`#form-${bfContentId}`).addEventListener('bf-form-reset', ({detail:{formId}}) => {\n	/* Write your code here... */\n})"},{lbl:"On Form Validation Error",val:"/* On Form Validation Error */\ndocument.querySelector(`#form-${bfContentId}`).addEventListener('bf-form-validation-error', ({detail:{formId, fieldId, error}}) => {\n	/* Write your code here... */\n})"}]},{type:"group-title",name:"Field Events"},{type:"group-accordion",name:"Text Field",childs:[le("change"),le("input"),le("blur"),le("focus")]},{type:"group-accordion",name:"Textarea Field",childs:[le("change"),le("input"),le("blur"),le("focus")]},{type:"group-accordion",name:"Email Field",childs:[le("change"),le("input"),le("blur"),le("focus")]},{type:"group-accordion",name:"Checkbox",childs:[le("change")]},{type:"group-accordion",name:"Select",childs:[le("change")]},{type:"group-accordion",name:"Button",childs:[le("click")]},{type:"group-opts",name:"Filter Functions",childs:[{lbl:"Filter Logic status",val:`function bf_modify_workflow_logic_status(logicStatus, logics, fieldValues, rowIndex, condIndx, props) {
	/* write your code here */ 
	return logicStatus
}`},{lbl:"Filter Razorpay Notes",val:`function bf_modify_razorpay_notes(notes) {
	 /* write your code here */ 
	return notes
}`}]}],Fo=[{type:"group-opts",name:"Field Keys",childs:[...kn()]}],No=k=>{let f=0;return k.reduce((n,l)=>(l.type?(n.push(l),f=0):(f||(n.push({type:"no-group",childs:[]}),f=1),n[n.length-1].childs.push(l)),n),[])};function zo({options:k,action:f}){var p;const{css:n}=hn(),l=No(k),[T,e]=he.useState(l);he.useEffect(()=>{e(l)},[k]);const a=s=>{const o=s.target.value.toLowerCase().trim();if(!o)return e(l);const m=qr(l).reduce((_,C)=>(C.type!=="group-title"&&(_.push(C),C.childs&&(_[_.length-1].childs=C.childs.filter(b=>b.lbl.toLowerCase().includes(o)),_[_.length-1].childs.length===0&&_.pop())),_),[]);e(m)},u=()=>{g.current.value="",e(l)},g=he.useRef(null);return W.jsxs("div",{className:n(ce.main),children:[W.jsxs("div",{className:n(ce.fields_search),children:[W.jsx("input",{ref:g,title:"Search Field","aria-label":"Search Field",autoComplete:"off","data-testid":"tlbr-srch-inp",placeholder:"Search...",id:"search-icon",type:"search",name:"searchIcn",onChange:a,className:n(ce.search_field)}),((p=g==null?void 0:g.current)==null?void 0:p.value)&&W.jsx("span",{title:"clear",className:n(ce.clear_icn),role:"button",tabIndex:"-1",onClick:u,onKeyDown:u,children:" "}),W.jsx("span",{title:"search",className:n(ce.search_icn),children:W.jsx(jr,{size:"20"})})]}),W.jsx(Hr,{style:{height:"92%"},autoHide:!0,children:W.jsx("div",{className:n(ce.groupList),children:T.map(s=>W.jsxs(he.Fragment,{children:[s.type==="group-accordion"&&W.jsx(ni,{title:s.name,children:W.jsx("ul",{className:n(ce.ul),children:"childs"in s&&s.childs.map(o=>W.jsx("li",{className:n(ce.li),children:W.jsx("button",{type:"button",className:`${n(ce.button)} btnHover`,title:o.lbl,onClick:()=>f(o.val),children:o.lbl})},`childs-${o.val}`))})},`group-accordion-${s.name}`),s.type==="group-opts"&&W.jsxs("ul",{className:n(ce.ul),children:[s.type.match(/group-opts|group-title/)&&W.jsx("h4",{className:n(ce.title),children:s.name}),"childs"in s&&s.childs.map(o=>W.jsx("li",{className:n(ce.li),children:W.jsx("button",{type:"button",className:`${n(ce.button)} btnHover`,title:o.lbl,onClick:()=>f(o.val),children:o.lbl})},`group-child-${o.val}`))]}),s.type==="group-title"&&W.jsx("h4",{className:n(ce.title),children:s.name})]},`group-acc-${s.name}`))})})]})}const ce={main:{h:300,w:200,py:3,ow:"hidden"},title:{m:0,pt:7,pb:5,pn:"sticky",tp:0,bd:"#fff",zx:9},fields_search:{pn:"relative",tn:"width .2s"},search_field:{mx:2,w:"98%",oe:"none",b:"none !important",brs:"9px !important",pl:"27px !important",pr:"5px !important",bd:"var(--white-0-97) !important",":focus":{oe:"none",bs:"0px 0px 0px 1.5px var(--b-50) !important",pr:"0px !important","& ~ .shortcut":{dy:"none"},"& ~ span svg":{cr:"var(--b-50)"}},"::placeholder":{fs:12},"::-webkit-search-cancel-button":{appearance:"none"}},search_icn:{pn:"absolute",tp:"50%",mx:6,lt:0,tm:"translateY(-50%)",cr:"var(--white-0-50)",curp:1,"& svg":{dy:"block"}},clear_icn:{pn:"absolute",tp:"50%",mx:6,rt:0,tm:"translateY(-50%)",cr:"var(--white-0-50)",curp:1,w:14,h:14,bd:"var(--white-0-83)",brs:20,backgroundPosition:"54% 50% !important",bi:`url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='Black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E")`},groupList:{mt:10},ul:{mt:0,mb:10},li:{mb:0,mt:5,ml:5},button:{fw:"normal",brs:5,dy:"block",w:"100%",ta:"left",b:0,bd:"none",p:3,curp:1,"&:hover":{bd:"var(--white-0-95)",cr:"var(--black-0)"},fs:11}};function Qo(){const{css:k}=hn(),{formType:f,formID:n}=Ur(),[l,T]=he.useState("JavaScript"),[e,a]=he.useState(localStorage.getItem("bf-editor-theme")||"tomorrow"),[u,g]=he.useState(localStorage.getItem("bf-enable-editor")||"on"),p=he.useRef({}),[s,o]=Wr(Kr),d=Vr(Xr),[m,_]=he.useState(Do),C=["JavaScript","CSS"],b=[{label:"Light Theme",value:"tomorrow"},{label:"Dark Theme",value:"twilight"}],y=c=>{c&&!(c in p.current)&&(p.current[l]=c)},A=c=>{o(h=>We(Oe({},h),{[l]:c}))},I=c=>{const h=p.current[l],{editor:x}=h;x.session.insert(x.getCursorPosition(),c);const w=x.getValue();o($=>We(Oe({},$),{[l]:w})),h.editor.renderer.scrollBarV.scrollTop!==h.editor.renderer.scrollBarV.maxScrollTop&&h.editor.gotoLine(h.editor.session.getLength()+1)},R=c=>{localStorage.setItem("bf-editor-theme",c),a(c)},S=c=>{const{checked:h}=c.target;h?(g("on"),localStorage.setItem("bf-enable-editor","on")):(g("off"),localStorage.setItem("bf-enable-editor","off"))},t=c=>{if(!bt){d(Oe({show:!0},Yr.customCode));return}if(f==="new"){Jr("#update-btn").click();return}const x=ft({form_id:n,customCodes:s},"bitforms_add_custom_code").then(w=>w);Zr.promise(x,{loading:mt("Updating..."),success:w=>{var $;return(($=w==null?void 0:w.data)==null?void 0:$.message)||(w==null?void 0:w.data)},error:mt("Error occurred, Please try again.")}),c.preventDefault()},i=()=>{if(l==="JavaScript")return Po;if(l==="CSS")return Fo},r={mode:l.toLowerCase(),theme:e,name:l,value:s[l]||"",onChange:c=>{A(c)},height:"330px",width:"100%",placeholder:"Write your code here...",setOptions:m,ref:y};return he.useEffect(()=>{f==="edit"&&!(s.JavaScript||s.CSS)?ft({form_id:n},"bitforms_get_custom_code").then(h=>{var x,w;return o({JavaScript:(x=h==null?void 0:h.data)==null?void 0:x.JavaScript,CSS:(w=h==null?void 0:h.data)==null?void 0:w.CSS,isFetched:!0}),h}):f==="new"&&ft({form_id:n,customCodes:s},"bitforms_add_custom_code").then(h=>h)},[]),W.jsxs("div",{children:[W.jsxs("div",{className:k({flx:"between"}),children:[W.jsx("div",{className:k(ye.w10,{flx:"center",my:2,ml:27}),children:W.jsx(ri,{width:300,options:C.map(c=>({label:c})),onChange:c=>T(c),defaultActive:"JavaScript",actionValue:l,wideTab:!0})}),W.jsx("div",{className:k(ye.flxc),children:W.jsxs(ei,{place:"bottom-end",children:[W.jsx("button",{"data-testid":"titl-mor-opt-btn","data-close":!0,type:"button",className:k(Ve.btn),unselectable:"on",draggable:"false",style:{cursor:"pointer"},title:mt("Snippets"),children:W.jsx(ti,{size:"16"})}),W.jsx(zo,{options:i(),action:I})]})})]}),W.jsx(Zt,{open:l==="JavaScript",children:W.jsxs("div",{className:"pos-rel",children:[!bt&&W.jsx(Qt,{style:{left:0,width:"100%"}}),u==="on"?W.jsx(cn,We(Oe({},r),{onLoad:c=>{var h;(h=c==null?void 0:c.session)!=null&&h.$worker?c.session.$worker.send("changeOptions",[{asi:!0}]):console.warn("worker not found")}})):W.jsx("textarea",{className:k(Ve.editor,{h:330}),onChange:c=>A(c.target.value),value:s[l]||"",rows:"18"})]})}),W.jsx(Zt,{open:l==="CSS",children:W.jsxs("div",{className:"pos-rel",children:[!bt&&W.jsx(Qt,{style:{left:0,width:"100%"}}),u==="on"?W.jsx(cn,Oe({},r)):W.jsx("textarea",{className:k(Ve.editor,{h:330}),onChange:c=>A(c.target.value),value:s[l]||"",rows:"18"})]})}),W.jsxs("div",{className:k(ye.flxb,ye.mt1,ye.mb1,{jc:"between"}),children:[W.jsxs("div",{className:k(ye.flxc,ye.w10,Ve.editorBtn),children:[W.jsx(Jt,{className:k(ye.mr2),title:"Editor Mode",checked:u==="on",onChange:S}),u==="on"&&W.jsxs(W.Fragment,{children:[W.jsx(Gr,{onChange:R,value:e,options:b,size:"sm",className:k({w:150})}),W.jsx(Jt,{className:k(ye.ml4),title:"Word Wrap",checked:m.wrap,onChange:()=>_(c=>We(Oe({},c),{wrap:!c.wrap}))})]})]}),W.jsx("button",{onClick:t,type:"button",className:k(ye.btn,Ve.saveBtn),children:"Save"})]})]})}const Ve={editor:{w:"99%"},btn:{b:0,brs:5,curp:1,flx:"center-between"},theme:{dy:"flex",jc:"flex-end"},editorBtn:{fs:12,pr:5},saveBtn:{bc:"var(--b-50)",brs:8,fs:13,fw:800,px:15,py:8,cr:"var(--white-100)",":hover":{bd:"var(--b-36)"}}},Do={autoScrollEditorIntoView:!0,enableBasicAutocompletion:!0,enableLiveAutocompletion:!0,enableSnippets:!0,showLineNumbers:!0,tabSize:2,animatedScroll:!0,showFoldWidgets:!0,displayIndentGuides:!0,enableEmmet:!0,enableMultiselect:!0,highlightSelectedWord:!0,fontSize:15,useSoftTabs:!0,showPrintMargin:!0,showGutter:!0,highlightActiveLine:!0};export{Qo as default};
