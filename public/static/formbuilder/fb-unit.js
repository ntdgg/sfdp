
//多选框
(function() {
  Formbuilder.registerField('checkboxes', {
    order: 10,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-square-o\"></span></span> 多选框",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
	  attrs.field_options.size = 'medium';
	  attrs.lists = 'no';
	  attrs.search = 'no';
	  attrs.type  = 'checkbox';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//日期组件
(function() {
  Formbuilder.registerField('date', {
    order: 20,
    view: "<div class='input-line'>\n  <input type='date' />\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-calendar\"></span></span> 日期",
	defaultAttributes: function(attrs) {
	  attrs.field_options.size = 'medium';
	  attrs.lists = 'no';
	  attrs.search = 'no';
	  attrs.type  = 'date';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//上传组件
(function() {
  Formbuilder.registerField('upload', {
    order: 0,
    view: "<img src='/static/formbuilder/upload.png' style='width:45px' ><input type='hidden' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class='symbol'><span class='fa fa-upload'></span></span> 上传组件",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.lists = 'yes';
	  attrs.search = 'yes';
	  attrs.type  = 'upload';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//下拉组件
(function() {
  Formbuilder.registerField('dropdown', {
    order: 24,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''></option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select>",
    edit: "<%= Formbuilder.templates['edit/options']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-caret-down\"></span></span> 下拉",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      attrs.field_options.size = 'medium';
	  attrs.lists = 'yes';
	  attrs.search = 'no';
	  attrs.type  = 'select';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//邮箱组件
(function() {
  Formbuilder.registerField('email', {
    order: 40,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-envelope-o\"></span></span> 邮箱",
	 defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.lists = 'yes';
	  attrs.search = 'no';
	  attrs.type  = 'text';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//单选框组件
(function() {
  Formbuilder.registerField('radio', {
    order: 15,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='radio' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='radio' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-circle-o\"></span></span> 单选框 ",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
	  attrs.field_options.size = 'medium';
	  attrs.lists = 'yes';
	  attrs.search = 'no';
	  attrs.type  = 'radio';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

//文本框控件
(function() {
  Formbuilder.registerField('text', {
    order: 0,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class='symbol'><span class='fa fa-font'></span></span> 文本控件",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.lists = 'yes';
	  attrs.search = 'yes';
	  attrs.type  = 'text';
	  attrs.fun  = 'no';
      return attrs;
    }
  });

}).call(this);

