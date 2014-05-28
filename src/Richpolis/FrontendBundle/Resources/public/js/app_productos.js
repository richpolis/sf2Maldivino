window.Productos = {};

Productos.Views = {};
Productos.Collections = {};
Productos.Models = {};
Productos.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Productos.Models.Categoria = Backbone.Model.extend({
    defaults: {
      nombre: '',
      isActive: true,
      position: 0,
      publicaciones:[],
    }
});

Productos.Collections.Categorias = Backbone.Collection.extend({
    model: Productos.Models.Categoria,
});

//vista de la lista de productos por categoria
Productos.Views.Show = Backbone.View.extend({

    el: '.productos',
    tagName: 'article',
    template: swig.compile($("#productos_template").html()),
    initialize: function() {
        this.model.on("change", this.render, this);
    },
    
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
        return this;
    },
    seleccionarCategoria: function(){
        var id = this.model.get('id');
        $(".categoria").removeClass('active');
        $("#categoria-"+id).addClass('active');
    },
});

Productos.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "categoria/:slug" : "show"
    },
    root: function() {
        debugger;
        $(".loader").fadeIn();
        var model = window.collections.categorias.get(window.api.datos[0].id);
        if(!window.api.accion.show){
            if(!window.views.show){
                window.views.show = new  Productos.Views.Show({model: model});
            }else{
                window.views.show.model= model;
            }
            views.show.render();
            $(".loader").fadeOut();
        }else{
            window.views.show.model= model;
            views.show.render().$el.fadeIn("fast");
            $(".loader").fadeOut();
        }
        window.views.show.seleccionarCategoria();
    },
    show: function(slug) {
        debugger;
        $(".loader").fadeIn();
        var models = window.collections.categorias.where({'slug':slug});
        if(!window.api.accion.show){
            if(!window.views.show){
                window.views.show = new  Productos.Views.Show({model: models[0]});
            }else{
                window.views.show.model= models[0];
            }
            views.show.render();
            $(".loader").fadeOut();
        }else{
            window.views.show.model= models[0];
            views.show.render().$el.fadeIn("fast");
            $(".loader").fadeOut();
        }
        window.views.show.seleccionarCategoria();
    },
});
