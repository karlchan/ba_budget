//TreeMap object.
var TM = {};

(function() {
/*
  Script: Core.js
  
  Description:
  
  Provides common utility functions and the Class object used internally by the library.
  
  Also provides the <TM.Util> object for manipulating JSON tree structures
  
  Some of the Basic utility functions and the Class system are based in the MooTools Framework <http://mootools.net>. Copyright (c) 2006-2010 Valerio Proietti, <http://mad4milk.net/>. MIT license <http://mootools.net/license.txt>.
  
  Author: 
  
  Nicolas Garcia Belmonte
  
  Copyright: 
  
  Copyright 2008-2010 by Nicolas Garcia Belmonte.
  
  Homepage: 
  
  <http://thejit.org>
  
  Version: 
  
  1.0

  License: 
  
  BSD License
 
> Redistribution and use in source and binary forms, with or without
> modification, are permitted provided that the following conditions are met:
>      * Redistributions of source code must retain the above copyright
>        notice, this list of conditions and the following disclaimer.
>      * Redistributions in binary form must reproduce the above copyright
>        notice, this list of conditions and the following disclaimer in the
>        documentation and/or other materials provided with the distribution.
>      * Neither the name of the organization nor the
>        names of its contributors may be used to endorse or promote products
>        derived from this software without specific prior written permission.
>
>  THIS SOFTWARE IS PROVIDED BY Nicolas Garcia Belmonte ``AS IS'' AND ANY
>  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
>  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
>  DISCLAIMED. IN NO EVENT SHALL Nicolas Garcia Belmonte BE LIABLE FOR ANY
>  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
>  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
>  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
>  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
>  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
>  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

var $ = function(d) { return document.getElementById(d); };

$.empty = function() {};

$.lambda = function(value) { 
  return (typeof value == 'function') ? value : function(){
    return value;
  };
};

$.extend = function(original, extended){
    for (var key in (extended || {})) original[key] = extended[key];
    return original;
};

$.splat = function(obj){
    var type = $.type(obj);
    return (type) ? ((type != 'array') ? [obj] : obj) : [];
};

$.type = function(elem) {
  return $.type.s.call(elem).match(/^\[object\s(.*)\]$/)[1].toLowerCase();
};
$.type.s = Object.prototype.toString;

$.each = function(iterable, fn){
  var type = $.type(iterable);
  if(type == 'object') {
    for (var key in iterable) fn(iterable[key], key);
  } else {
    for(var i=0; i < iterable.length; i++) fn(iterable[i], i);
  }
};

$.merge = function(){
    var mix = {};
    for (var i = 0, l = arguments.length; i < l; i++){
        var object = arguments[i];
        if ($.type(object) != 'object') continue;
        for (var key in object){
            var op = object[key], mp = mix[key];
            mix[key] = (mp && $.type(op) == 'object' && $.type(mp) == 'object') ? $.merge(mp, op) : $.unlink(op);
        }
    }
    return mix;
};

$.unlink = function(object){
    var unlinked;
    switch ($.type(object)){
        case 'object':
            unlinked = {};
            for (var p in object) unlinked[p] = $.unlink(object[p]);
        break;
        case 'array':
            unlinked = [];
            for (var i = 0, l = object.length; i < l; i++) unlinked[i] = $.unlink(object[i]);
        break;
        default: return object;
    }
    return unlinked;
};

$.rgbToHex = function(srcArray, array){
    if (srcArray.length < 3) return null;
    if (srcArray.length == 4 && srcArray[3] == 0 && !array) return 'transparent';
    var hex = [];
    for (var i = 0; i < 3; i++){
        var bit = (srcArray[i] - 0).toString(16);
        hex.push((bit.length == 1) ? '0' + bit : bit);
    }
    return (array) ? hex : '#' + hex.join('');
};

$.hexToRgb = function(hex) {
  if(hex.length != 7) {
    hex = hex.match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
    hex.shift();
    if (hex.length != 3) return null;
    var rgb = [];
    for(var i=0; i<3; i++) {
      var value = hex[i];
      if (value.length == 1) value += value;
      rgb.push(parseInt(value, 16));
    }
    return rgb;
  } else {
    hex = parseInt(hex.slice(1), 16);
    return [
      hex >> 16,
      hex >> 8 & 0xff,
      hex & 0xff
    ];
  }
}

$.destroy = function(elem) {
   $.clean(elem);
   if(elem.parentNode) elem.parentNode.removeChild(elem);
   if(elem.clearAttributes) elem.clearAttributes(); 
};

$.clean = function(elem) {
  for(var ch = elem.childNodes, i=0; i < ch.length; i++) {
      $.destroy(ch[i]);
  }  
};

$.addEvent = function(obj, type, fn) {
    if (obj.addEventListener) 
        obj.addEventListener(type, fn, false);
    else 
        obj.attachEvent('on' + type, fn);
};

$.hasClass = function(obj, klass) {
    return (' ' + obj.className + ' ').indexOf(' ' + klass + ' ') > -1;
};

$.addClass = function(obj, klass) {
    if(!$.hasClass(obj, klass)) obj.className = (obj.className + " " + klass);
};

$.removeClass = function(obj, klass) {
  obj.className = obj.className.replace(new RegExp('(^|\\s)' + klass + '(?:\\s|$)'), '$1');
};

$.getPos = function(elem) {
  if(elem.getBoundingClientRect) {
    var bound = elem.getBoundingClientRect(), html = elem.ownerDocument.documentElement;
    return {
      x: bound.left + html.scrollLeft - html.clientLeft,
      y: bound.top +  html.scrollTop  - html.clientTop
    };
  }
  
  var offset = getOffsets(elem);
  var scroll = getScrolls(elem);
  
  return {x: offset.x - scroll.x, y: offset.y - scroll.y};
  
  function getOffsets(elem) {
    var position = { x: 0, y: 0 };
    while (elem && !isBody(elem)){
      position.x += elem.offsetLeft;
      position.y += elem.offsetTop;
      elem = elem.offsetParent;
    }
    return position;
  }
  
  function getScrolls(elem){
    var position = {x: 0, y: 0};
    while (elem && !isBody(elem)){
      position.x += elem.scrollLeft;
      position.y += elem.scrollTop;
      elem = elem.parentNode;
    }
    return position;
  }

  function isBody(element){
    return (/^(?:body|html)$/i).test(element.tagName);
  }
};

var Class = function(properties){
  properties = properties || {};
  var klass = function(){
      for (var key in this){
          if (typeof this[key] != 'function') this[key] = $.unlink(this[key]);
      }
      this.constructor = klass;
      if (Class.prototyping) return this;
      var instance = (this.initialize) ? this.initialize.apply(this, arguments) : this;
      return instance;
  };
  
  for (var mutator in Class.Mutators){
      if (!properties[mutator]) continue;
      properties = Class.Mutators[mutator](properties, properties[mutator]);
      delete properties[mutator];
  }
  
  $.extend(klass, this);
  klass.constructor = Class;
  klass.prototype = properties;
  return klass;
};

Class.Mutators = {

    Implements: function(self, klasses){
        $.each($.splat(klasses), function(klass){
            Class.prototying = klass;
            var instance = (typeof klass == 'function')? new klass : klass;
            for(var prop in instance) {
              if(!(prop in self)) {
                self[prop] = instance[prop];
              }
            }
            delete Class.prototyping;
        });
        return self;
    }

};

$.extend(Class, {

    inherit: function(object, properties){
        var caller = arguments.callee.caller;
        for (var key in properties){
            var override = properties[key];
            var previous = object[key];
            var type = $.type(override);
            if (previous && type == 'function'){
                if (override != previous){
                    if (caller){
                        override.__parent = previous;
                        object[key] = override;
                    } else {
                        Class.override(object, key, override);
                    }
                }
            } else if(type == 'object'){
                object[key] = $.merge(previous, override);
            } else {
                object[key] = override;
            }
        }

        if (caller) object.parent = function(){
            return arguments.callee.caller.__parent.apply(this, arguments);
        };

        return object;
    },

    override: function(object, name, method){
        var parent = Class.prototyping;
        if (parent && object[name] != parent[name]) parent = null;
        var override = function(){
            var previous = this.parent;
            this.parent = parent ? parent[name] : object[name];
            var value = method.apply(this, arguments);
            this.parent = previous;
            return value;
        };
        object[name] = override;
    }

});


Class.prototype.implement = function(){
    var proto = this.prototype;
    $.each(Array.prototype.slice.call(arguments || []), function(properties){
        Class.inherit(proto, properties);
    });
    return this;
};

var Event = {
  getPos: function(e, win) {
    // get mouse position
    win = win  || window;
    e = e || win.event;
    var doc = win.document;
    doc = doc.html || doc.body;
    var page = {
        x: e.pageX || e.clientX + doc.scrollLeft,
        y: e.pageY || e.clientY + doc.scrollTop
    };
    return page;
  }
};

/*
   Object: TM.Util

   Some common JSON tree manipulation methods.
*/
TM.Util = {

    /*
       Method: prune
    
       Clears all tree nodes having depth greater than maxLevel.
    
       Parameters:
    
          tree - A JSON tree object. For more information please see <Loader.loadJSON>.
          maxLevel - An integer specifying the maximum level allowed for this tree. All nodes having depth greater than max level will be deleted.

    */
    prune: function(tree, maxLevel) {
        this.each(tree, function(elem, i) {
            if(i == maxLevel && elem.children) {
                delete elem.children;
                elem.children = [];
            }
        });
    },
    
    /*
       Method: getParent
    
       Returns the parent node of the node having _id_ as id.
    
       Parameters:
    
          tree - A JSON tree object. See also <Loader.loadJSON>.
          id - The _id_ of the child node whose parent will be returned.

      Returns:

          A tree JSON node if any, or false otherwise.
    
    */
    getParent: function(tree, id) {
        if(tree.id == id) return false;
        var ch = tree.children;
        if(ch && ch.length > 0) {
            for(var i=0; i<ch.length; i++) {
                if(ch[i].id == id) 
                    return tree;
                else {
                    var ans = this.getParent(ch[i], id);
                    if(ans) return ans;
                }
            }
        }
        return false;       
    },

    /*
       Method: getSubtree
    
       Returns the subtree that matches the given id.
    
       Parameters:
    
          tree - A JSON tree object. See also <Loader.loadJSON>.
          id - A node *unique* identifier.
    
       Returns:
    
          A subtree having a root node matching the given id. Returns null if no subtree matching the id is found.

    */
    getSubtree: function(tree, id) {
        if(tree.id == id) return tree;
        for(var i=0, ch=tree.children; i<ch.length; i++) {
            var t = this.getSubtree(ch[i], id);
            if(t != null) return t;
        }
        return null;
    },

    /*
       Method: getLeaves
    
        Returns the leaves of the tree.
    
       Parameters:
    
          node - A JSON tree node. See also <Loader.loadJSON>.
          maxLevel - _optional_ A subtree's max level.
    
       Returns:
    
       An array having objects with two properties. 
       
        - The _node_ property contains the leaf node. 
        - The _level_ property specifies the depth of the node.

    */
    getLeaves: function (node, maxLevel) {
        var leaves = [], levelsToShow = maxLevel || Number.MAX_VALUE;
        this.each(node, function(elem, i) {
            if(i < levelsToShow && 
            (!elem.children || elem.children.length == 0 )) {
                leaves.push({
                    'node':elem,
                    'level':levelsToShow - i
                });
            }
        });
        return leaves;
    },


    /*
       Method: eachLevel
    
        Iterates on tree nodes with relative depth less or equal than a specified level.
    
       Parameters:
    
          tree - A JSON tree or subtree. See also <Loader.loadJSON>.
          initLevel - An integer specifying the initial relative level. Usually zero.
          toLevel - An integer specifying a top level. This method will iterate only through nodes with depth less than or equal this number.
          action - A function that receives a node and an integer specifying the actual level of the node.
            
      Example:
     (start code js)
       TM.Util.eachLevel(tree, 0, 3, function(node, depth) {
          alert(node.name + ' ' + depth);
       });
     (end code)
    */
    eachLevel: function(tree, initLevel, toLevel, action) {
        if(initLevel <= toLevel) {
            action(tree, initLevel);
            for(var i=0, ch = tree.children; i<ch.length; i++) {
                this.eachLevel(ch[i], initLevel +1, toLevel, action);   
            }
        }
    },

    /*
       Method: each
    
        A tree iterator.
    
       Parameters:
    
          tree - A JSON tree or subtree. See also <Loader.loadJSON>.
          action - A function that receives a node.

      Example:
      (start code js)
        TM.Util.each(tree, function(node) {
          alert(node.name);
        });
      (end code)
            
    */
    each: function(tree, action) {
        this.eachLevel(tree, 0, Number.MAX_VALUE, action);
    },
    
    /*
       Method: loadSubtrees
    
        Appends subtrees to leaves by requesting new subtrees
        with the _request_ method.
    
       Parameters:
    
          tree - A JSON tree node. <Loader.loadJSON>.
          controller - An object that implements a request method.
      
       Example:
        (start code js)
          TM.Util.loadSubtrees(leafNode, {
            request: function(nodeId, level, onComplete) {
              //Pseudo-code to make an ajax request for a new subtree
              // that has as root id _nodeId_ and depth _level_ ...
              Ajax.request({
                'url': 'http://subtreerequesturl/',
                
                onSuccess: function(json) {
                  onComplete.onComplete(nodeId, json);
                }
              });
            }
          });
        (end code)
    */
    loadSubtrees: function(tree, controller) {
        var maxLevel = controller.request && controller.levelsToShow;
        var leaves = this.getLeaves(tree, maxLevel),
        len = leaves.length,
        selectedNode = {};
        if(len == 0) controller.onComplete();
        for(var i=0, counter=0; i<len; i++) {
            var leaf = leaves[i], id = leaf.node.id;
            selectedNode[id] = leaf.node;
            controller.request(id, leaf.level, {
                onComplete: function(nodeId, tree) {
                    var ch = tree.children;
                    selectedNode[nodeId].children = ch;
                    if(++counter == len) {
                        controller.onComplete();
                    }
                }
            });
        }
    }
};


/*
 * File: Options.js
 * 
 * Visualization common options.
 *
 */

/*
 * Object: Options
 * 
 * Parent object for common Options.
 *
 */
var Options = function() {
  var args = Array.prototype.slice.call(arguments);
  for(var i=0, l=args.length, ans={}; i<l; i++) {
    var opt = Options[args[i]];
    if(opt.$extend) {
      $.extend(ans, opt);
    } else {
      ans[args[i]] = opt;  
    }
  }
  return ans;
};

/*
  Object: Options.Controller
  
  Provides controller methods.
  
  Description:
  
  You can implement controller functions inside the configuration object of all visualizations.
  
  *Common to all visualizations*
    
   - _onBeforeCompute(node)_ This method is called right before performing all computation and animations to the JIT visualization.
   - _onAfterCompute()_ This method is triggered right after all animations or computations for the JIT visualizations ended.

  *Used in <Canvas> based visualizations <ST>, <Hypertree>, <RGraph>*

   - _onCreateLabel(domElement, node)_ This method receives the label dom element as first parameter, and the corresponding <Graph.Node> as second parameter. This method will only be called on label creation. Note that a <Graph.Node> is a node from the input tree/graph you provided to the visualization. If you want to know more about what kind of JSON tree/graph format is used to feed the visualizations, you can take a look at <Loader.loadJSON>. This method proves useful when adding events to the labels used by the JIT.
   - _onPlaceLabel(domElement, node)_ This method receives the label dom element as first parameter and the corresponding <Graph.Node> as second parameter. This method is called each time a label has been placed on the visualization, and thus it allows you to update the labels properties, such as size or position. Note that onPlaceLabel will be triggered after updating the labels positions. That means that, for example, the left and top css properties are already updated to match the nodes positions.
   - _onBeforePlotNode(node)_ This method is triggered right before plotting a given node. The _node_ parameter is the <Graph.Node> to be plotted. 
    This method is useful for changing a node style right before plotting it.
   - _onAfterPlotNode(node)_ This method is triggered right after plotting a given node. The _node_ parameter is the <Graph.Node> plotted.
   - _onBeforePlotLine(adj)_ This method is triggered right before plotting an edge. The _adj_ parameter is a <Graph.Adjacence> object. 
    This method is useful for adding some styles to a particular edge before being plotted.
   - _onAfterPlotLine(adj)_ This method is triggered right after plotting an edge. The _adj_ parameter is the <Graph.Adjacence> plotted.

   *Used in <TM> (Treemap) and DOM based visualizations*
    
   - _onCreateElement(content, node, isLeaf, elem1, elem2)_ This method is called on each newly created node. 
    
    Parameters:
     content - The div wrapper element with _content_ className.
     node - The corresponding JSON tree node (See also <Loader.loadJSON>).
     isLeaf - Whether is a leaf or inner node. If the node's an inner tree node, elem1 and elem2 will become the _head_ and _body_ div elements respectively. 
     If the node's a _leaf_, then elem1 will become the div leaf element.
    
    - _onDestroyElement(content, node, isLeaf, elem1, elem2)_ This method is called before collecting each node. Takes the same parameters as onCreateElement.
    
    *Used in <ST> and <TM>*
    
    - _request(nodeId, level, onComplete)_ This method is used for buffering information into the visualization. When clicking on an empty node,
    the visualization will make a request for this node's subtrees, specifying a given level for this subtree (defined by _levelsToShow_). Once the request is completed, the _onComplete_ 
    object should be called with the given result.
 
 */
Options.Controller = {
  $extend: true,
  
  onBeforeCompute: $.empty,
  onAfterCompute:  $.empty,
  onCreateLabel:   $.empty,
  onPlaceLabel:    $.empty,
  onComplete:      $.empty,
  onBeforePlotLine:$.empty,
  onAfterPlotLine: $.empty,
  onBeforePlotNode:$.empty,
  onAfterPlotNode: $.empty,
  onCreateElement: $.empty,
  onDestroyElement:$.empty,
  request:         false
};

/*
  Object: Options.Tips
  
  Options for Tips
  
  Description:
  
  Options for Tool Tips.
  
  Implemented by:
  
  <TM>

  These configuration parameters are currently used by <TM>.


  - _enable_ If *true*, a tooltip will be shown when a node is hovered. The tooltip is a div DOM element having "tip" as CSS class. Default's *false*. 
  - _offsetX_ An offset added to the current tooltip x-position (which is the same as the current mouse position). Default's 20.
  - _offsetY_ An offset added to the current tooltip y-position (which is the same as the current mouse position). Default's 20.
  - _onShow(tooltip, node, isLeaf, domElement)_ Implement this method to change the HTML content of the tooltip when hovering a node.
  
  Parameters:
    tooltip - The tooltip div element.
    node - The corresponding JSON tree node (See also <Loader.loadJSON>).
    isLeaf - Whether is a leaf or inner node.
    domElement - The current hovered DOM element.

*/
Options.Tips = {
  $extend: false,
  
  enable: false, // TODO(nico) change allow for enable
  attachToDOM: true,
  attachToCanvas: false,
  offsetX: 20,
  offsetY: 20,
  onShow: $.empty
};

/*
 * File: Extras.js
 * 
 * Provides Extras such as Tips and Style Effects.
 * 
 * Description:
 * 
 * Provides the <Tips> and <NodeStyles> classes and functions.
 *
 */

/* 
 * Provides the initialization function for <NodeStyles> and <Tips> implemented 
 * by all main visualizations.
 *
 */
var Extras = {
  initializeExtras: function() {
    var tips = this.config.Tips;
    if(tips) {
      this.tips = new Tips(this);
    }
  }   
};


/*
   Class: Tips
    
   A class containing tip related functions. This class is used internally.
   
   Used by:
   
   <ST>, <Sunburst>, <Hypertree>, <RGraph>, <TM>, <ForceDirected>, <Icicle>
   
   See also:
   
   <Options.Tips>
*/

var Tips = new Class({
  initialize: function(viz) {
    this.viz = viz;
    this.controller = this.config = viz.config;
    // add tooltip
    if(this.config.Tips.enable && document.body) {
        var tip = document.getElementById('_tooltip') || document.createElement('div');
        tip.id = '_tooltip';
        tip.className = 'tip';
        var style = tip.style;
        style.position = 'absolute';
        style.display = 'none';
        style.zIndex = 13000;
        document.body.appendChild(tip);
        this.tip = tip;
        this.node = false;
    }
  },
  
  attach: function(node, elem) {
    if(this.config.Tips.enable) {
      var that = this, cont = this.controller;
      $.addEvent(elem, 'mouseover', function(e){
        cont.Tips.onShow(that.tip, node, elem);
      });
      $.addEvent(elem, 'mouseout', function(e){
          that.tip.style.display = 'none';
      });
      // Add mousemove event handler
      $.addEvent(elem, 'mousemove', function(e, win){
        var pos = Event.getPos(e, win);
        that.setTooltipPosition(pos);
      });
    }
  },

  onClick: $.empty,
  onRightClick: $.empty,
  
  onMousemove: function(node, opt) {
    if(!node) {
      this.tip.style.display = 'none';
      this.node = false;
      return;
    }
    if(!this.node || this.node.id != node.id) {
      this.node = node;
      this.config.Tips.onShow(this.tip, node);
    }
    this.setTooltipPosition(opt.position);
  },
  
  setTooltipPosition: function(pos) {
    var tip = this.tip, style = tip.style, cont = this.config;
    style.display = '';
    // get window dimensions
    win = {
      'height': document.body.clientHeight,
      'width': document.body.clientWidth
    };
    // get tooltip dimensions
    var obj = {
      'width': tip.offsetWidth,
      'height': tip.offsetHeight  
    };
    // set tooltip position
    var x = cont.Tips.offsetX, y = cont.Tips.offsetY;
    style.top = ((pos.y + y + obj.height > win.height)?  
        (pos.y - obj.height - y) : pos.y + y) + 'px';
    style.left = ((pos.x + obj.width + x > win.width)? 
        (pos.x - obj.width - x) : pos.x + x) + 'px';
  }  
});


/*
 * File: Treemap.js
 * 
 * Implements the <TM> class and other derived classes.
 *
 * Description:
 *
 * A Treemap is an information visualization technique, proven very useful when displaying large hierarchical structures on a constrained space. The idea behind a Treemap is to describe hierarchical relations as 'containment'. That means that if node B is child of node A, then B 'is contained' in A.
 *
 * Inspired by:
 *
 * Squarified Treemaps (Mark Bruls, Kees Huizing, and Jarke J. van Wijk) 
 *
 * <http://www.win.tue.nl/~vanwijk/stm.pdf>
 *
 * Tree visualization with tree-maps: 2-d space-filling approach (Ben Shneiderman)
 *
 * <http://hcil.cs.umd.edu/trs/91-03/91-03.html>
 *
 * Disclaimer:
 *
 * This visualization was built from scratch, taking only these papers as inspiration, and only shares some features with the Treemap papers mentioned above.
 *
 */

/*
   Class: TM

  Abstract Treemap class.
  
    Implements:
    
    <Tips>

   Implemented By:
    
    <TM.Squarified>, <TM.Strip> and <TM.SliceAndDice>.

    Description:
    
    Implements layout and configuration options inherited by <TM.Squarified>, <TM.Strip> and <TM.SliceAndDice>.

    All Treemap constructors take the same configuration object as parameter.

    Two special _data_ keys are read from the JSON tree structure loaded by <Loader.loadJSON> to calculate 
    node's color and dimensions. These properties are $.area (for nodes dimensions) and $.color. Both of these properties are floats.

    This means that the tree structure defined in <Loader.loadJSON> should now look more like this

    (start code js)
        var json = {  
            "id": "aUniqueIdentifier",  
            "name": "usually a nodes name",  
            "data": {
                "$.area": 33, //some float value
                "$.color": 36, //-optional- some float value
                "some key": "some value",
                "some other key": "some other value"
             },  
            "children": [ 'other nodes or empty' ]  
        };  
    (end code)

    If you want to know more about JSON tree structures and the _data_ property please read <Loader.loadJSON>.

    Configuration:

    *General*

    - _rootId_ The id of the div container where the Treemap will be injected. Default's 'infovis'.
    - _orientation_ For <TM.Strip> and <TM.SliceAndDice> only. The layout algorithm orientation. Possible values are 'h' or 'v'.
    - _levelsToShow_ Max depth of the plotted tree. Useful when using the request method.
    - _addLeftClickHandler_ Add a left click event handler to zoom in the Treemap view when clicking a node. Default's *false*. 
    - _addRightClickHandler_ Add a right click event handler to zoom out the Treemap view. Default's *false*.
    - _selectPathOnHover_ If setted to *true* all nodes contained in the path between the hovered node and the root node will have an *in-path* CSS class. Default's *false*.

    *Nodes*
    
    There are two kinds of Treemap nodes.

    (see treemapnode.png)

    Inner nodes are nodes having children, like _Pearl Jam_.
    
    These nodes are represented by three div elements. A _content_ element, a _head_ element (where the title goes) and a _body_ element, where the children are laid out.
    
    (start code xml)
    <div class="content">
      <div class="head">Pearl Jam</div>
      <div class="body">...other nodes here...</div>
    </div>
    (end code)

      Leaves are optionally colored nodes laying at the "bottom" of the tree. For example, _Yield_, _Vs._ and _Riot Act_ are leaves.

    These nodes are represented by two div elements. A _content_ element and a wrapped _leaf_ element

    (start code xml)
    <div class="content">
      <div class="leaf">Yield</div>
    </div>
    (end code)

    There are some configuration properties regarding Treemap nodes

    - _titleHeight_ The height of the title (_head_) div container. Default's 13.
    - _offset_ The separation offset between the _content_ div element and its contained div(s). Default's 4.

    *Color*

    _Color_ is an object containing as properties

    - _enable_ If *true*, the algorithm will check for the JSON node data _$.color_ property to add some color to the Treemap leaves. 
    This color is calculated by interpolating a node's $.color value range with a real RGB color range. 
    By specifying min|maxValues for the $.color property and min|maxColorValues for the RGB counterparts, the visualization is able to 
    interpolate color values and assign a proper color to the leaf node. Default's *false*.
    - _minValue_ The minimum value expected for the $.color value property. Used for interpolating. Default's -100.
    - _maxValue_ The maximum value expected for the $.color value property. Used for interpolating. Default's 100.
    - _minColorValue_ A three-element RGB array defining the color to be assigned to the _$.color_ having _minValue_ as value. Default's [255, 0, 50].
    - _maxColorValue_ A three-element RGB array defining the color to be assigned to the _$.color_ having _maxValue_ as value. Default's [0, 255, 50].

    *Tips*

    See <Options.Tips>.
    
    *Controller options*

    See <Options.Controller>.

    See also <TM.Squarified>, <TM.SliceAndDice> and <TM.Strip>.


*/
TM.Base = $.extend({
  layout: {
    orientation: "h",
    vertical: function() { 
      return this.orientation == "v"; 
    },
    horizontal: function() { 
      return this.orientation == "h"; 
    },
    change: function() { 
      this.orientation = this.vertical()? "h" : "v"; 
    }
  },
  
  config: {
    orientation: "h",
    titleHeight: 13,
    rootId: 'infovis',
    offset:4,
    levelsToShow: 3,
    addLeftClickHandler: false,
    addRightClickHandler: false,
    selectPathOnHover: false,
    
    Tips: Options.Tips,
    
    Color: {
      enable: false,
      minValue: -100,
      maxValue: 100,
      minColorValue: [255, 0, 50],
      maxColorValue: [0, 255, 50]
    }     
  },
  

  initialize: function(controller) {
    this.tree = null;
    this.shownTree = null;
    this.controller = this.config = $.merge(Options.Controller,
                    this.config,
                    controller);
    this.rootId = this.config.rootId;
    this.layout.orientation = this.config.orientation;
    // add tips
    this.initializeExtras();
    // purge
    var that = this;
    var fn = function() {
        that.empty();
        if(window.CollectGarbage) window.CollectGarbage();
        delete fn;
    };
    if(window.addEventListener) {
        window.addEventListener('unload', fn, false);
    } else {
        window.attachEvent('onunload', fn);
    }
  },

    /*
       Method: each
    
        Traverses head and leaf nodes applying a given function

      Parameters:
      
        f - A function that takes as parameters the same as the onCreateElement and onDestroyElement methods described in <TM>.
    */
    each: function(f) {  
    (function rec(elem) {
        if(!elem) return;
        var ch = elem.childNodes, len = ch.length;
        if(len > 0) {
            f.apply(this, [elem, len === 1, ch[0], ch[1]]);
        }
        if (len > 1) {
          for(var chi = ch[1].childNodes, i=0; i<chi.length; i++) {
              rec(chi[i]);
          }
        }  
      })($(this.rootId).firstChild);
    },

  /*
     toStyle
  
    Transforms a JSON into a CSS style string.
  */
  toStyle: function(obj) {
    var ans = "";
    for(var s in obj) ans += s + ":" + obj[s] + ";";
    return ans;
  },

  /*
     leaf
  
    Returns a boolean value specifying if the node is a tree leaf or not.
  
     Parameters:
  
        tree - A tree node (which is also a JSON tree object of course). <http://blog.thejit.org>

     Returns:
  
         A boolean value specifying if the node is a tree leaf or not.
 
  */
  leaf: function(tree) {
    return tree.children == 0;
  },

  /*
     Method: createBox
  
    Constructs the proper DOM layout from a json node.
    
        If the node's an _inner node_, 
        this method calls <TM.contentBox>, <TM.bodyBox> and <TM.leafBox> 
        to create the following HTML structure
        
        (start code xml)
        <div class="content">
          <div class="head">[Node name]</div>
          <div class="body">[Node's children]</div>
        </div>
        (end code)

        If the node's a leaf node, it creates the following structure 
        by calling <TM.contentBox>, <TM.leafBox>

        (start code xml)
        <div class="content">
          <div class="leaf">[Node name]</div>
        </div>
        (end code)


     Parameters:

        json - A JSON subtree. See also <Loader.loadJSON>. 
        coord - A coordinates object specifying width, height, left and top style properties.
        html - html to inject into the _body_ element if the node is an inner Tree node.

      Returns:

          The HTML structure described above.

      See also:

        <TM>, <TM.contentBox>, <TM.bodyBox>, <TM.headBox>, <TM.leafBox>.

  */
  createBox: function(json, coord, html) {
    var box;
    if(!this.leaf(json)) {
      box = this.headBox(json, coord) + this.bodyBox(html, coord);
    } else {
      box = this.leafBox(json, coord);
    }
    return this.contentBox(json, coord, box);
  },
  
  /*
     Method: plot
  
    Renders the Treemap.

      Parameters:

        json - A JSON tree structure preprocessed by some Treemap layout algorithm.

      Returns:

        The HTML to inject to the main visualization container.

      See also:

        <TM.createBox>.


  */
  plot: function(json) {
    var coord = json.coord, html = "";
    
    if(this.leaf(json)) 
      return this.createBox(json, coord, null);
    
    for(var i=0, ch=json.children; i<ch.length; i++) {
      var chi = ch[i], chcoord = chi.coord;
      // skip tiny nodes
      if(chcoord.width * chcoord.height > 1) {
        html+= this.plot(chi);  
      }
    } 
    return this.createBox(json, coord, html);
  },


  /*
     Method: headBox
  
    Creates the _head_ div dom element that usually contains the name of a parent JSON tree node.
  
     Parameters:
  
        json - A JSON subtree. See also <Loader.loadJSON>.
        coord - width and height base coordinate object.

     Returns:
  
         A new _head_ div dom element that has _head_ as class name.

        See also:

          <TM.createBox>.
 
  */
  headBox: function(json, coord) {
    var config = this.config, offst = config.offset;
    var c = {
      'height': config.titleHeight + "px",
      'width': (coord.width - offst) + "px",
      'left':  offst / 2 + "px"
    };
    return "<div class=\"head\" style=\"" + this.toStyle(c) + "\">"
         + json.name + "</div>";
  },

  /*
     Method: bodyBox
  
    Creates the _body_ div dom element that usually contains a subtree dom element layout.
  
     Parameters:
  
        html - html that should be contained in the body html.
        coord - width and height base coordinate object.

     Returns:
  
         A new _body_ div dom element that has _body_ as class name.
 
        See also:

          <TM.createBox>.
 
  */
  bodyBox: function(html, coord) {
    var config = this.config,
    th = config.titleHeight,
    offst = config.offset;
    var c = {
      'width': (coord.width - offst) + "px",
      'height':(coord.height - offst - th) + "px",
      'top':   (th + offst / 2) +  "px",
      'left':  (offst / 2) + "px"
    };
    return "<div class=\"body\" style=\""
      + this.toStyle(c) +"\">" + html + "</div>";
  },



  /*
     Method: contentBox
  
    Creates the _content_ div dom element that usually contains a _leaf_ div dom element or _head_ and _body_ div dom elements.
  
     Parameters:
  
        json - A JSON node. See also <Loader.loadJSON>. 
        coord - An object containing width, height, left and top coordinates.
        html - input html wrapped by this tag.
        
     Returns:
  
         A new _content_ div dom element that has _content_ as class name.

       See also:

          <TM.createBox>.
 
  */
  contentBox: function(json, coord, html) {
    var c = {};
    for(var i in coord) c[i] = coord[i] + "px";
    return "<div class=\"content\" style=\"" + this.toStyle(c) 
       + "\" id=\"" + json.id + "\">" + html + "</div>";
  },


  /*
     Method: leafBox
  
    Creates the _leaf_ div dom element that usually contains nothing else.
  
     Parameters:
  
        json - A JSON subtree. See also <Loader.loadJSON>. 
        coord - base with and height coordinate object.
        
     Returns:
  
         A new _leaf_ div dom element having _leaf_ as class name.
 
       See also:

          <TM.createBox>.
 

  */
  leafBox: function(json, coord) {
    var config = this.config;
    var backgroundColor = config.Color.enable && this.setColor(json), 
    offst = config.offset,
    width = coord.width - offst,
    height = coord.height - offst;
    var c = {
      'top':   (offst / 2)  + "px",
      'height':height + "px",
      'width': width + "px",
      'left': (offst / 2) + "px"
    };
    if(backgroundColor) c['background-color'] = backgroundColor;
    return "<div class=\"leaf\" style=\"" + this.toStyle(c) + "\">" 
        + json.name + "</div>";
  },


  /*
     Method: setColor
  
        Calculates an hexa color string based on the _$.color_ data node property.  
  
          This method is called by <TM.leafBox> to assign an hexadecimal color to each leaf node.
          
          This color is calculated by making a linear interpolation between _$.color_ max and min values and 
          RGB max and min values so that

          > hex = (maxColorValue - minColorValue) / (maxValue - minValue) * (x - minValue) + minColorValue

          where _x_ range is [minValue, maxValue] and 

          - _minValue_
          - _maxValue_
          - _minColorValue_
          - _maxColorValue_

        are defined in the <TM> configuration object.

        This method is called by <TM.leafBox> iif _Color.enable_ is setted to _true_.

        Sometimes linear interpolation for coloring is just not enough. In that case you can re-implement this 
        method so that it fits your coloring needs.

        Some people might find useful to implement their own coloring interpolation method and to assign the resulting hex string 
        to the _$.color_ property. In that case we could re-implement the <TM.setColor> method like this

        (start code js)
          //TM.Strip, TM.SliceAndDice also work
          TM.Squarified.implement({
            'setColor': function(json) {
              return json.data.$color;
            }
          });
        (end code)

      So that it returns the previously assigned hex string.

     Parameters:
  
        json - A JSON tree node.

     Returns:
  
         A String that represents a color in hex value.
 
  */
  setColor: function(json) {
    var c = this.config.Color,
    maxcv = c.maxColorValue,
    mincv = c.minColorValue,
    maxv = c.maxValue,
    minv = c.minValue,
    diff = maxv - minv,
    x = (json.data.$color - 0);
    // linear interpolation
    var comp = function(i, x) { 
      return Math.round((((maxcv[i] - mincv[i]) / diff) * (x - minv) + mincv[i])); 
    };
    
    return $.rgbToHex([ comp(0, x), comp(1, x), comp(2, x) ]);
  },

  /*
     Method: enter
  
    Sets the _elem_ parameter as root and performs the layout.
  
     Parameters:
  
        elem - A JSON Tree node. See also <Loader.loadJSON>. 
  */
  enter: function(elem) {
    this.view(elem.parentNode.id);
  },
  
    /*
       Method: onLeftClick
    
        Sets the _elem_ parameter as root and performs the layout. 
        This method is called when _addLeftClickHandler_ is *true* and a 
        node is left-clicked. You can override this method to add some custom behavior 
        when the node is left clicked though.
        
        An Example for overriding this method could be
        (start code js)
        //TM.Strip or TM.SliceAndDice also work
        TM.Squarified.implement({
            'onLeftClick': function(elem) {
                //some custom code...
            }
        });
        (end code)
        
    
       Parameters:
    
          elem - A JSON Tree node. See also <Loader.loadJSON>.
          
       See also:
       
          <TM.enter>
    */
    onLeftClick: function(elem) {
        this.enter(elem);
    },

  /*
     Method: out
  
    Sets the _parent_ node of the currently shown subtree as root and performs the layout.
  
  */
  out: function() {
    var parent = TM.Util.getParent(this.tree, this.shownTree.id);
    if(parent) {
      if(this.controller.request)
        TM.Util.prune(parent, this.config.levelsToShow);
      this.view(parent.id);
    }
  },
  
    /*
       Method: onRightClick
    
        Sets the _parent_ node of the currently shown subtree as root and performs the layout. 
        This method is called when _addRightClickHandler_ is *true* and a 
        node is right-clicked. You can override this method to add some custom behavior 
        when the node is right-clicked though.

        An Example for overriding this method could be
        (start code js)
        //TM.Strip or TM.SliceAndDice also work
        TM.Squarified.implement({
            'onRightClick': function() {
                //some custom code...
            }
        });
        (end code)

       See also:
       
          <TM.out>

    */
    onRightClick: function() {
        this.out();
    },

  /*
     Method: view
  
    Sets the root of the treemap to the specified node id and performs the layout.
  
     Parameters:
  
      id - A node identifier
  */
  view: function(id) {
    var config = this.config, that = this;
    var post = {
      onComplete: function() {
        that.loadTree(id);
        $(config.rootId).focus();
      }
    };

    if (this.controller.request) {
      var TUtil = TM.Util;
      TUtil.loadSubtrees(TUtil.getSubtree(this.tree, id),
               $.merge(this.controller, post));
    } else {
      post.onComplete();
    }
  },
  
  /*
     Method: resetPath
  
       Sets an 'in-path' className for _leaf_ and _head_ elements which belong to the path between the given tree node 
       and the visualization's root node.
  
     Parameters:
  
        tree - A JSON  tree node. See also <Loader.loadJSON>.
  */
  resetPath: function(tree) {
    var root = this.rootId, previous = this.resetPath.previous;
    this.resetPath.previous = tree || false;
    function getParent(c) { 
        var p = c.parentNode;
        return p && (p.id != root) && p;
     };
     function toggleInPath(elem, remove) {
        if(elem) {
            var container = $(elem.id);
            if(container) {
                var parent = getParent(container);
                while(parent) {
                    elem = parent.childNodes[0];
                    if($.hasClass(elem, 'in-path')) {
                        if(remove == undefined || !!remove) $.removeClass(elem, 'in-path');
                    } else {
                        if(!remove) $.addClass(elem, 'in-path');
                    }
                    parent = getParent(parent);
                }
            }
        }
     };
     toggleInPath(previous, true);
     toggleInPath(tree, false);                
  },

    
    /*
       Method: initializeElements
    
       Traverses the DOM tree applying the onCreateElement method.

       The onCreateElement controller method should attach events and add some behavior to the DOM element
       node created. *By default, the Treemap wont add any event to its elements.*
    */
    initializeElements: function() {
      var cont = this.controller, that = this;
      var ff = $.lambda(false);
      this.each(function(content, isLeaf, elem1, elem2) {
          var tree = TM.Util.getSubtree(that.tree, content.id);
          cont.onCreateElement(content, tree, isLeaf, elem1, elem2);

          // eliminate context menu when right clicking
          if(cont.addRightClickHandler) elem1.oncontextmenu = ff;

          // add click handlers
          if(cont.addLeftClickHandler || cont.addRightClickHandler) {
            $.addEvent(elem1, 'mouseup', function(e) {
                var rightClick = (e.which == 3 || e.button == 2);
                if (rightClick) {
                    if(cont.addRightClickHandler) that.onRightClick();
                }                     
                else {
                    if(cont.addLeftClickHandler) that.onLeftClick(elem1);
                } 
                    
                // prevent default
                if (e.preventDefault) 
                    e.preventDefault();
                else 
                    e.returnValue = false;
            });
          }
          
          // add path selection on hovering nodes
          if(cont.selectPathOnHover) {
            $.addEvent(elem1, 'mouseover', function(e){
                if(cont.selectPathOnHover) {
                    if (isLeaf) {
                        $.addClass(elem1, 'over-leaf');
                    }
                    else {
                        $.addClass(elem1, 'over-head');
                        $.addClass(content, 'over-content');
                    }
                    if (content.id) 
                        that.resetPath(tree);
                }
            });
            
            $.addEvent(elem1, 'mouseout', function(e){
                if(cont.selectPathOnHover) {
                    if (isLeaf) {
                        $.removeClass(elem1, 'over-leaf');
                    }
                    else {
                        $.removeClass(elem1, 'over-head');
                        $.removeClass(content, 'over-content');
                    }
                    that.resetPath();
                }
            });
          }
          
          // attach tips
         that.tips.attach(tree, elem1);
      });
    },

    /*
       Method: destroyElements
    
       Traverses the tree applying the onDestroyElement method.

       The onDestroyElement controller method should detach events and garbage collect the element.
       *By default, the Treemap adds some garbage collect facilities for IE.*
    */
    destroyElements: function() {
      if(this.controller.onDestroyElement != $.empty) {
          var cont = this.controller, that = this;
          this.each(function(content, isLeaf, elem1, elem2) {
              cont.onDestroyElement(content, TM.Util.getSubtree(that.tree, content.id), isLeaf, elem1, elem2);
          });
      }  
    },
    
    /*
       Method: empty
    
        Empties the Treemap container (trying also to garbage collect things).
    */
    empty: function() {
        this.destroyElements();
        $.clean($(this.rootId));
    },

  /*
     Method: loadTree
  
    Loads the subtree specified by _id_ and plots it on the layout container.
  
     Parameters:
  
        id - A subtree id.
  */
  loadTree: function(id) {
    this.empty();
    this.loadJSON(TM.Util.getSubtree(this.tree, id));
  }
  
}, Extras);

/*
   Class: TM.SliceAndDice

  A JavaScript implementation of the Slice and Dice Treemap algorithm.

  The <TM.SliceAndDice> constructor takes an _optional_ configuration object described in <TM>.

    This visualization (as all other Treemap visualizations) is fed with JSON Tree structures.

    The _$.area_ node data key is required for calculating rectangles dimensions.

    The _$.color_ node data key is required if _Color_ _allow_ is *true* and is used for calculating 
    leaves colors.

    Extends:
    <TM>, <Tips>

    Parameters:

    config - Configuration defined in <TM>.

    Example:


  Here's a way of instanciating the <TM.SliceAndDice> will all its _optional_ configuration features
  
  (start code js)

  var tm = new TM.SliceAndDice({
    orientation: "h",
    titleHeight: 13,
    rootId: 'infovis',
    offset:4,
    levelsToShow: 3,
    addLeftClickHandler: false,
    addRightClickHandler: false,
    selectPathOnHover: false,
            
    Color: {
      enable: false,
      minValue: -100,
      maxValue: 100,
      minColorValue: [255, 0, 50],
      maxColorValue: [0, 255, 50]
    },
    
    Tips: {
      enable: false,
      offsetX; 20,
      offsetY: 20,
      onShow: function(tooltip, node, domElement) {}
    },
      
    onBeforeCompute:  function(node) {
      //Some stuff on before compute...
    },
    onAfterCompute:   function() {
      //Some stuff on after compute...
    },
    onCreateElement:  function(content, node, isLeaf, head, body) {
      //Some stuff onCreateElement
    },
    onDestroyElement: function(content, node, isLeaf, head, body) {
      //Some stuff onDestroyElement
    },
    request:          false
  });
  tm.loadJSON(json);

  (end code)

*/
TM.SliceAndDice = new Class({
  Implements: TM.Base,
  /*
     Method: loadJSON
  
    Loads the specified JSON tree and lays it on the main container.
  
     Parameters:
  
        json - A JSON Tree. See also <Loader.loadJSON>. 
  */
  loadJSON: function (json) {
    this.controller.onBeforeCompute(json);
    var container = $(this.rootId),
    config = this.config,
    width = container.offsetWidth,
    height = container.offsetHeight;
    
    var p = {
      'coord': {
        'top': 0,
        'left': 0,
        'width':  width,
        'height': height + config.titleHeight + config.offset
      }
    };
    
    if(this.tree == null) this.tree = json;
    this.shownTree = json;
    this.compute(p, json, this.layout.orientation);
    container.innerHTML = this.plot(json);
        this.initializeElements();
    this.controller.onAfterCompute(json);
  },
  
  /*
     Method: compute
  
    Called by loadJSON to calculate recursively all node positions and lay out the tree.
  
     Parameters:

        par - The parent node of the json subtree.  
        json - A JSON subtree. See also <Loader.loadJSON>.
        orientation - The current orientation. This value is switched recursively.
  */
  compute: function(par, json, orientation) {
    var config = this.config, 
    coord = par.coord,
    offst = config.offset,
    width  = coord.width - offst,
    height = coord.height - offst - config.titleHeight,
    pdata = par.data,
    fact = (pdata && ("$.area" in pdata))? json.data.$area / pdata.$area : 1;
    var otherSize, size, dim, pos, pos2;
    
    var horizontal = (orientation == "h");
    if(horizontal) {
      orientation = 'v';    
      otherSize = height;
      size = Math.round(width * fact);
      dim = 'height';
      pos = 'top';
      pos2 = 'left';
    } else {
      orientation = 'h';    
      otherSize = Math.round(height * fact);
      size = width;
      dim = 'width';
      pos = 'left';
      pos2 = 'top';
    }
    json.coord = {
      'width':size,
      'height':otherSize,
      'top':0,
      'left':0
    };
    var offsetSize = 0, tm = this;
    $.each(json.children, function(elem){
      tm.compute(json, elem, orientation);
      elem.coord[pos] = offsetSize;
      elem.coord[pos2] = 0;
      offsetSize += Math.floor(elem.coord[dim]);
    });
  }
});


/*
   Class: TM.Area

  Abstract Treemap class containing methods that are common to
   aspect ratio related algorithms such as <TM.Squarified> and <TM.Strip>.

    Implemented by:

    <TM.Squarified>, <TM.Strip>
*/
TM.Area = {

  /*
     Method: loadJSON
  
    Loads the specified JSON tree and lays it on the main container.
  
     Parameters:
  
        json - A JSON tree. See also <Loader.loadJSON>.
  */
  loadJSON: function (json) {
    this.controller.onBeforeCompute(json);
    var container = $(this.rootId),
    width = container.offsetWidth,
    height = container.offsetHeight,
    offst = this.config.offset,
    offwdth = width - offst,
    offhght = height - offst - this.config.titleHeight;

    json.coord =  {
      'height': height,
      'width': width,
      'top': 0,
      'left': 0
    };
    var coord = $.merge(json.coord, {
      'width': offwdth,
      'height': offhght
    });

    this.compute(json, coord);
    container.innerHTML = this.plot(json);
    if(this.tree == null) this.tree = json;
    this.shownTree = json;
    this.initializeElements();
    this.controller.onAfterCompute(json);
  },
  
  /*
     Method: computeDim
  
    Computes dimensions and positions of a group of nodes
    according to a custom layout row condition. 
  
     Parameters:

        tail - An array of nodes.  
          initElem - An array of nodes (containing the initial node to be laid).
        w - A fixed dimension where nodes will be layed out.
        coord - A coordinates object specifying width, height, left and top style properties.
        comp - A custom comparison function
  */
  computeDim: function(tail, initElem, w, coord, comp) {
    if(tail.length + initElem.length == 1) {
      var l = (tail.length == 1)? tail : initElem;
      this.layoutLast(l, w, coord);
      return;
    }
    if(tail.length >= 2 && initElem.length == 0) {
      initElem = [tail[0]];
      tail = tail.slice(1);
    }
    if(tail.length == 0) {
      if(initElem.length > 0) this.layoutRow(initElem, w, coord);
      return;
    }
    var c = tail[0];
    if(comp(initElem, w) >= comp([c].concat(initElem), w)) {
      this.computeDim(tail.slice(1), initElem.concat([c]), w, coord, comp);
    } else {
      var newCoords = this.layoutRow(initElem, w, coord);
      this.computeDim(tail, [], newCoords.dim, newCoords, comp);
    }
  },

  
  /*
     Method: worstAspectRatio
  
    Calculates the worst aspect ratio of a group of rectangles. 
        
        See also:
        
        <http://en.wikipedia.org/wiki/Aspect_ratio>
    
     Parameters:

      ch - An array of nodes.  
        w - The fixed dimension where rectangles are being laid out.

     Returns:
  
         The worst aspect ratio.
 

  */
  worstAspectRatio: function(ch, w) {
    if(!ch || ch.length == 0) return Number.MAX_VALUE;
    var areaSum = 0, maxArea = 0, minArea = Number.MAX_VALUE;
    for(var i=0; i<ch.length; i++) {
      var area = ch[i]._area;
      areaSum += area; 
      minArea = (minArea < area)? minArea : area;
      maxArea = (maxArea > area)? maxArea : area; 
    }
    var sqw = w * w, sqAreaSum = areaSum * areaSum;
    return Math.max(sqw * maxArea / sqAreaSum,
            sqAreaSum / (sqw * minArea));
  },
  
  /*
     Method: avgAspectRatio
  
    Calculates the average aspect ratio of a group of rectangles. 
        
        See also:
        
        <http://en.wikipedia.org/wiki/Aspect_ratio>
    
     Parameters:

      ch - An array of nodes.  
        w - The fixed dimension where rectangles are being laid out.

     Returns:
  
         The average aspect ratio.
 

  */
  avgAspectRatio: function(ch, w) {
    if(!ch || ch.length == 0) return Number.MAX_VALUE;
    var arSum = 0;
    for(var i=0; i<ch.length; i++) {
      var area = ch[i]._area;
      var h = area / w;
      arSum += (w > h)? w / h : h / w;
    }
    return arSum / ch.length;
  },

  /*
     layoutLast
  
    Performs the layout of the last computed sibling.
  
     Parameters:

        ch - An array of nodes.  
        w - A fixed dimension where nodes will be layed out.
      coord - A coordinates object specifying width, height, left and top style properties.
  */
  layoutLast: function(ch, w, coord) {
    ch[0].coord = coord;
  }
  
};




/*
   Class: TM.Squarified

  A JavaScript implementation of the Squarified Treemap algorithm.

  The <TM.Squarified> constructor takes an _optional_ configuration object described in <TM>.

    This visualization (as all other Treemap visualizations) is fed with JSON Tree structures.

    The _$.area_ node data key is required for calculating rectangles dimensions.

    The _$.color_ node data key is required if _Color_ _allow_ is *true* and is used for calculating 
    leaves colors.

    Extends:
    <TM>, <TM.Area>

    Parameters:

    config - Configuration defined in <TM>.

    Example:


  Here's a way of instanciating the <TM.Squarified> will all its _optional_ configuration features
  
  (start code js)

  var tm = new TM.Squarified({
    titleHeight: 13,
    rootId: 'infovis',
    offset:4,
    levelsToShow: 3,
    addLeftClickHandler: false,
    addRightClickHandler: false,
    selectPathOnHover: false,
            
    Color: {
      enable: false,
      minValue: -100,
      maxValue: 100,
      minColorValue: [255, 0, 50],
      maxColorValue: [0, 255, 50]
    },
          
    Tips: {
      enable: false,
      offsetX: 20,
      offsetY: 20,
      onShow: function(tooltip, node, domElement) {}
    },
  
    onBeforeCompute:  function(node) {
      //Some stuff on before compute...
    },
    onAfterCompute:   function() {
      //Some stuff on after compute...
    },
    onCreateElement:  function(content, node, isLeaf, head, body) {
      //Some stuff onCreateElement
    },
    onDestroyElement: function(content, node, isLeaf, head, body) {
      //Some stuff onDestroyElement
    },
    request:          false
  });

  tm.loadJSON(json);

  (end code)

*/
  
TM.Squarified = new Class({
  Implements: [TM.Base, TM.Area],

  /*
     Method: compute
  
    Called by loadJSON to calculate recursively all node positions and lay out the tree.
  
     Parameters:

        json - A JSON tree. See also <Loader.loadJSON>.
        coord - A coordinates object specifying width, height, left and top style properties.
  */
  compute: function(json, coord) {
    if (!(coord.width >= coord.height && this.layout.horizontal())) 
      this.layout.change();
    var ch = json.children, config = this.config;
    if(ch.length > 0) {
      this.processChildrenLayout(json, ch, coord);
      for(var i=0; i<ch.length; i++) {
        var chcoord = ch[i].coord,
        offst = config.offset,
        height = chcoord.height - (config.titleHeight + offst),
        width = chcoord.width - offst;
        coord = {
          'width':width,
          'height':height,
          'top':0,
          'left':0
        };
        this.compute(ch[i], coord);
      }
    }
  },

  /*
     Method: processChildrenLayout
  
    Computes children real areas and other useful parameters for performing the Squarified algorithm.
  
     Parameters:

        par - The parent node of the json subtree.  
        ch - An Array of nodes
      coord - A coordinates object specifying width, height, left and top style properties.
  */
  processChildrenLayout: function(par, ch, coord) {
    // compute children real areas
    var parentArea = coord.width * coord.height;
    var i, totalChArea=0, chArea = [];
    for(i=0; i < ch.length; i++) {
      chArea[i] = parseFloat(ch[i].data.$area);
      totalChArea += chArea[i];
    }
    for(i=0; i<chArea.length; i++) {
      ch[i]._area = parentArea * chArea[i] / totalChArea;
    }
    var minimumSideValue = (this.layout.horizontal())? coord.height : coord.width;
    ch.sort(function(a, b) { return (a._area <= b._area) - (a._area >= b._area); });
    var initElem = [ch[0]];
    var tail = ch.slice(1);
    this.squarify(tail, initElem, minimumSideValue, coord);
  },

  /*
    Method: squarify
  
    Performs an heuristic method to calculate div elements sizes in order to have a good aspect ratio.
  
     Parameters:

        tail - An array of nodes.  
        initElem - An array of nodes, containing the initial node to be laid out.
        w - A fixed dimension where nodes will be laid out.
        coord - A coordinates object specifying width, height, left and top style properties.
  */
  squarify: function(tail, initElem, w, coord) {
    this.computeDim(tail, initElem, w, coord, this.worstAspectRatio);
  },
  
  /*
     Method: layoutRow
  
    Performs the layout of an array of nodes.
  
     Parameters:

        ch - An array of nodes.  
        w - A fixed dimension where nodes will be laid out.
        coord - A coordinates object specifying width, height, left and top style properties.
  */
  layoutRow: function(ch, w, coord) {
    if(this.layout.horizontal()) {
      return this.layoutV(ch, w, coord);
    } else {
      return this.layoutH(ch, w, coord);
    }
  },
  
  layoutV: function(ch, w, coord) {
    var totalArea = 0, rnd = Math.round; 
    $.each(ch, function(elem) { totalArea += elem._area; });
    var width = rnd(totalArea / w), top =  0; 
    for(var i=0; i<ch.length; i++) {
      var h = rnd(ch[i]._area / width);
      ch[i].coord = {
        'height': h,
        'width': width,
        'top': coord.top + top,
        'left': coord.left
      };
      top += h;
    }
    var ans = {
      'height': coord.height,
      'width': coord.width - width,
      'top': coord.top,
      'left': coord.left + width
    };
    // take minimum side value.
    ans.dim = Math.min(ans.width, ans.height);
    if(ans.dim != ans.height) this.layout.change();
    return ans;
  },
  
  layoutH: function(ch, w, coord) {
    var totalArea = 0, rnd = Math.round; 
    $.each(ch, function(elem) { totalArea += elem._area; });
    var height = rnd(totalArea / w),
    top = coord.top, 
    left = 0;
    
    for(var i=0; i<ch.length; i++) {
      ch[i].coord = {
        'height': height,
        'width': rnd(ch[i]._area / height),
        'top': top,
        'left': coord.left + left
      };
      left += ch[i].coord.width;
    }
    var ans = {
      'height': coord.height - height,
      'width': coord.width,
      'top': coord.top + height,
      'left': coord.left
    };
    ans.dim = Math.min(ans.width, ans.height);
    if(ans.dim != ans.width) this.layout.change();
    return ans;
  }
});


/*
   Class: TM.Strip

  A JavaScript implementation of the Strip Treemap algorithm.

  The <TM.Strip> constructor takes an _optional_ configuration object described in <TM>.

    This visualization (as all other Treemap visualizations) is fed with JSON Tree structures.

    The _$.area_ node data key is required for calculating rectangles dimensions.

    The _$.color_ node data key is required if _Color_ _allow_ is *true* and is used for calculating 
    leaves colors.

    Extends:
    <TM>, <TM.Area>

    Parameters:
    
    config - Configuration defined in <TM>.

    Example:


  Here's a way of instanciating the <TM.Strip> will all its _optional_ configuration features
  
  (start code js)

  var tm = new TM.Strip({
    titleHeight: 13,
    orientation: "h",
    rootId: 'infovis',
    offset:4,
    levelsToShow: 3,
    addLeftClickHandler: false,
    addRightClickHandler: false,
    selectPathOnHover: false,
            
    Color: {
      enable: false,
      minValue: -100,
      maxValue: 100,
      minColorValue: [255, 0, 50],
      maxColorValue: [0, 255, 50]
    },
    
    Tips: {
      enable: false,
      offsetX: 20,
      offsetY: 20,
      onShow: function(tooltip, node, domElement) {}
    },

    onBeforeCompute:  function(node) {
      //Some stuff on before compute...
    },
    onAfterCompute:   function() {
      //Some stuff on after compute...
    },
    onCreateElement:  function(content, node, isLeaf, head, body) {
      //Some stuff onCreateElement
    },
    onDestroyElement: function(content, node, isLeaf, head, body) {
      //Some stuff onDestroyElement
    },
    request:          false
  });
  
  tm.loadJSON(json);

  (end code)

*/
  
TM.Strip = new Class({
  Implements: [TM.Base, TM.Area],

  /*
     Method: compute
  
    Called by loadJSON to calculate recursively all node positions and lay out the tree.
  
     Parameters:

        json - A JSON subtree. See also <Loader.loadJSON>. 
      coord - A coordinates object specifying width, height, left and top style properties.
  */
  compute: function(json, coord) {
    var ch = json.children, config = this.config;
    if(ch.length > 0) {
      this.processChildrenLayout(json, ch, coord);
      for(var i=0; i<ch.length; i++) {
        var chcoord = ch[i].coord,
        offst = config.offset,
        height = chcoord.height - (config.titleHeight + offst),
        width = chcoord.width - offst;
        coord = {
          'width':width,
          'height':height,
          'top':0,
          'left':0
        };
        this.compute(ch[i], coord);
      }
    }
  },

  /*
     Method: processChildrenLayout
  
    Computes children real areas and other useful parameters for performing the Strip algorithm.
  
     Parameters:

      par - The parent node of the json subtree.  
      ch - An Array of nodes
      coord - A coordinates object specifying width, height, left and top style properties.
  */
  processChildrenLayout: function(par, ch, coord) {
    // compute children real areas
    var area = coord.width * coord.height;
    var dataValue = parseFloat(par.data.$area);
    $.each(ch, function(elem) {
      elem._area = area * parseFloat(elem.data.$area) / dataValue;
    });
    var side = (this.layout.horizontal())? coord.width : coord.height;
    var initElem = [ch[0]];
    var tail = ch.slice(1);
    this.stripify(tail, initElem, side, coord);
  },

  /*
     Method: stripify
  
    Performs an heuristic method to calculate div elements sizes in order to have 
    a good compromise between aspect ratio and order.
  
     Parameters:

        tail - An array of nodes.  
        initElem - An array of nodes.
        w - A fixed dimension where nodes will be layed out.
      coord - A coordinates object specifying width, height, left and top style properties.
  */
  stripify: function(tail, initElem, w, coord) {
    this.computeDim(tail, initElem, w, coord, this.avgAspectRatio);
  },
  
  /*
     Method: layoutRow
  
    Performs the layout of an array of nodes.
  
     Parameters:

        ch - An array of nodes.  
        w - A fixed dimension where nodes will be laid out.
        coord - A coordinates object specifying width, height, left and top style properties.
  */
  layoutRow: function(ch, w, coord) {
    if(this.layout.horizontal()) {
      return this.layoutH(ch, w, coord);
    } else {
      return this.layoutV(ch, w, coord);
    }
  },
  
  layoutV: function(ch, w, coord) {
  // TODO(nico): handle node dimensions properly
    var totalArea = 0, rnd = function(x) { return x; }; // Math.round;
    $.each(ch, function(elem) { totalArea += elem._area; });
    var width = rnd(totalArea / w), top =  0; 
    for(var i=0; i<ch.length; i++) {
      var h = rnd(ch[i]._area / width);
      ch[i].coord = {
        'height': h,
        'width': width,
        'top': coord.top + (w - h - top),
        'left': coord.left
      };
      top += h;
    }

    var ans = {
      'height': coord.height,
      'width': coord.width - width,
      'top': coord.top,
      'left': coord.left + width,
      'dim': w
    };
    return ans;
  },
  
  layoutH: function(ch, w, coord) {
    var totalArea = 0, rnd = function(x) { return x; }; // Math.round;
    $.each(ch, function(elem) { totalArea += elem._area; });
    var height = rnd(totalArea / w),
    top = coord.height - height, 
    left = 0;
    
    for(var i=0; i<ch.length; i++) {
      ch[i].coord = {
        'height': height,
        'width': rnd(ch[i]._area / height),
        'top': top,
        'left': coord.left + left
      };
      left += ch[i].coord.width;
    }
    var ans = {
      'height': coord.height - height,
      'width': coord.width,
      'top': coord.top,
      'left': coord.left,
      'dim': w
    };
    return ans;
  }
});

})();    
