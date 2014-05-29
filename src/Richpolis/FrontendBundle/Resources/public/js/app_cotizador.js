window.Productos = {};

Productos.Views = {};
Productos.Collections = {};
Productos.Models = {};
Productos.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Productos.Models.Producto = Backbone.Model.extend({
    defaults: {
      producto: '',
      precio: 0,
      cantidad: 0,
      seleccionado: false,
    }
});

Productos.Collections.Productos = Backbone.Collection.extend({
    model: Productos.Models.Producto,
});

//vista de la lista de productos
Productos.Views.ListProductos = Backbone.View.extend({

    el: '#listaProductos',
    tagName: 'tbody',
    template: swig.compile($("#productos_template").html()),
    initialize: function() {
        this.collection.on("change", this.render, this);
    },
    render: function() {
        var data = this.collection.toJSON();
        this.$el.html(this.template(data));
        return this;
    },
    seleccionarProducto: function(){
        
    },
});

//vista de la lista de productos
Productos.Views.ListFormulario = Backbone.View.extend({

    el: '#listaFormulario',
    tagName: 'tbody',
    template: swig.compile($("#formulario_template").html()),
    initialize: function() {
        this.collection.on("change", this.render, this);
    },
    render: function() {
        var data = this.collection.toJSON();
        this.$el.html(this.template(data));
        return this;
    },
    seleccionarProducto: function(){
        
    },
});

Productos.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root"
    },
    root: function() {
        window.views.listProductos = new Productos.Views.ListProductos({
            collection: window.collections.productos,
        });
        window.views.listFormulario = new Productos.Views.ListFormulario({
            collection: window.collections.productos,
        });
        views.listProductos.render();
        views.listFormulario.render();
    },
    
});
