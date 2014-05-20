window.Autobuses = {};

Autobuses.Views = {};
Autobuses.Collections = {};
Autobuses.Models = {};
Autobuses.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Autobuses.Models.Autobus = Backbone.Model.extend({
    defaults: {
      nombre: '',
      isActive: true,
      position: 0,
    }
});

Autobuses.Collections.Autobuses = Backbone.Collection.extend({
    model: Autobuses.Models.Autobus,
});

//esta vista es para visualizar el show_template
Autobuses.Views.Show = Backbone.View.extend({
    el: '.autobus',
    tagName: 'article',
    template: swig.compile($("#show_template").html()),
    events: {
        'click .control-anterior': 'anterior',
        'click .control-siguiente' : 'siguiente',
    },
    initialize: function() {
        this.model.on("change", this.render, this);
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
        return this;
    },
    mostrar: function(){
        var self = this;
        $(".autobuses").fadeOut("slow",function(){
           self.$el.fadeIn("fast");
           self.iniciarComponentes();
        });
        window.api.accion.list=false;
        window.api.accion.show=true;
    },
    anterior: function(e){
        e.preventDefault();
        debugger;
        var id = $(".control-anterior").data('anterior');
        window.routers.app.navigate("show/"+id,true);
    },
    siguiente: function(e){
        e.preventDefault();
        debugger;
        var id = $(".control-siguiente").data('siguiente');
        window.routers.app.navigate("show/"+id,true);
    },
    iniciarComponentes: function(){
        iniciarSlider();
        iniciarFancybox();
    }
});


//vista collecio que recibe todos los modelos.
Autobuses.Views.ListView = Backbone.View.extend({

    el: '.autobuses',
    tagName: 'article',
    /*template: swig.compile($("#table_template").html()),*/
    initialize: function() {
    
    },
    
    render: function() {
    
    },
    
    mostrar: function(){
        var self = this;
        $(".autobus").fadeOut("slow",function(){
           self.$el.fadeIn("fast");
        });
        window.api.accion.list=true;
        window.api.accion.show=false;
    }
});

Autobuses.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "show/:id" : "show"
    },
    root: function() {
        debugger;
        if(!window.api.accion.list){
            window.views.listview = new Autobuses.Views.ListView({
                collection: window.collections.autobuses,
            });
            views.listview.mostrar();
        }
    },
    show: function(id) {
        debugger;
        var model = window.collections.autobuses.get(id);
        if(!window.api.accion.show){
            if(!window.views.show){
                window.views.show = new  Autobuses.Views.Show({model: model});
            }else{
                window.views.show.model= model;
            }
            views.show.render();
            views.show.mostrar();
        }else{
            window.views.show.model= model;
            views.show.render().$el.fadeIn("fast");
            window.views.show.iniciarComponentes();
        }
    },
});
