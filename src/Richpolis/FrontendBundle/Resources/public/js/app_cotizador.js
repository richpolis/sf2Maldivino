window.Productos = {};

Productos.Views = {};
Productos.Collections = {};
Productos.Models = {};
Productos.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};

var formatNumber = {
	 separador: ",", // separador para los miles
	 sepDecimal: '.', // separador para los decimales
	 formatear:function (num){
	 	num +='';
	  	var splitStr = num.split(',');
	  	var splitLeft = splitStr[0];
	  	var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
	  	var regx = /(\d+)(\d{3})/;
	  	while (regx.test(splitLeft)) {
	  		splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
	  	}
	  	return this.simbol + splitLeft  +splitRight;
	 },
	 new:function(num, simbol){
	  	this.simbol = simbol ||'';
	  	return this.formatear(num);
	 }
};

Productos.Models.Producto = Backbone.Model.extend({
    defaults: {
      	producto: '',
      	precio: 0,
      	cantidad: 0,
      	seleccionado: false,
	  	get_precio: '',
		get_importe: '',
		importe: 0,
    },
	initialize: function () {
        this.on("change:cantidad", function (self) {
             var cantidad = self.get("cantidad");
			this.set({get_importe: this.getImporte()});
		});
		this.set({get_precio: this.getPrecio()});
    },
	getImporte: function(){
		this.set({importe: this.get('precio')*this.get('cantidad')});
		return formatNumber.new(this.get('importe'),"$");
	},
	getPrecio: function(){
		return formatNumber.new(this.get('precio'),"$");
	},
});

Productos.Collections.Productos = Backbone.Collection.extend({
    model: Productos.Models.Producto,
	importeTotal: function(){
		debugger;
		var productos = this.where({seleccionado: true});
		var importe = 0;
		for(var cont=0; cont<productos.length; cont++){
			importe += productos[cont].get('importe');
		}
		return importe;
	},
	quitarSeleccionados: function(){
		this.each(function (producto) {
			if(producto.get('seleccionado')==true){
				producto.set({cantidad: 0,seleccionado: false});
			}
		});	
	},
});

//vista de la lista de productos
Productos.Views.Buscador = Backbone.View.extend({
    el: '.buscador',
    tagName: 'article',
});

//vista de un item de la lista de productos
Productos.Views.ItemProductos = Backbone.View.extend({
	tagName: 'tr',
	className: 'productoItem',
    template: swig.compile($("#productos_template").html()),
	events: {
      "dblclick" : "agregarFormulario",
    },
    initialize: function() {
        this.model.on("change", this.render, this);
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
		this.$el.attr({id: 'producto-' + this.model.get('id')});
        return this;
    },
	agregarFormulario: function(e){
		e.preventDefault();
		this.model.set({seleccionado: true,cantidad: 1});
		views.listFormulario.render();
		this.$el.fadeOut();
		this.remove();
	},
});

//vista de la lista de productos
Productos.Views.ListProductos = Backbone.View.extend({
    el: '#listaProductos',
    tagName: 'tbody',
    initialize: function() {
        this.collection.on("change", this.render, this);
    },
	AddOne: function(producto) {
    	var indice = 0;
    	if(window.views.productos && window.views.productos.length){
    		indice = window.views.productos.length;
    	}else{
    		indice = 0;
    		window.views.productos = [];
    	}
		if(producto.get('seleccionado')==false){
    		window.views.productos[indice]= new Productos.Views.ItemProductos({model: producto});
        	var html = window.views.productos[indice].render().el;
        	this.$el.append(html);
		}	
    },
    render: function() {
    	this.borrarViewsItems();
        this.collection.forEach(this.AddOne,this);
        return this;
    },
    borrarViewsItems: function() {
    	var indice = 0;
    	if(window.views.productos && window.views.productos.length){
    		indice = window.views.productos.length;
    	}else{
    		indice = 0;
    		window.views.productos = [];
    	}
    	for(var cont = 0; cont < indice; cont++ ){
    		window.views.productos[cont].remove();
    	}
    },
});

//vista de un item de la lista de productos
Productos.Views.ItemFormulario = Backbone.View.extend({
	tagName: 'tr',
	className: 'productoItemSel',
    template: swig.compile($("#formulario_template").html()),
	events: {
      "click .productoItemCantidadUp" : "masUno",
	  "click .productoItemCantidadDown" : "menosUno",
	  "keypress input.productoItemCantidad": "actualizarCantidad", 	
    },
    initialize: function() {
        this.model.on("change", this.render, this);
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
		this.$el.attr({id: 'producto-' + this.model.get('id')});
        return this;
    },
    masUno: function(){
		var cantidad = this.model.get('cantidad');
		this.model.set({cantidad: cantidad+1});
    },
	menosUno: function(){
		var cantidad = this.model.get('cantidad');
		if(cantidad>0){
			this.model.set({cantidad: cantidad-1});
		}	
    },
	actualizarCantidad: function(e){
		e.preventDefault();
		var cantidad = this.$el.find('input.productoItemCantidad').val();
		if(cantidad!==this.model.get('cantidad')){
			this.model.set({cantidad: cantidad});
		}
	}

});

//vista de la lista de productos
Productos.Views.ListFormulario = Backbone.View.extend({
    el: '#listaFormulario',
    tagName: 'tbody',
    template: swig.compile($("#formulario_template").html()),
    initialize: function() {
        this.collection.on("change", this.render, this);
    },
	AddOne: function(producto) {
    	var indice = 0;
    	if(window.views.inputs && window.views.inputs.length){
    		indice = window.views.inputs.length;
    	}else{
    		indice = 0;
    		window.views.inputs = [];
    	}
		if(producto.get('seleccionado')==true){
    		window.views.inputs[indice]= new Productos.Views.ItemFormulario({model: producto});
        	var html = window.views.inputs[indice].render().el;
        	this.$el.append(html);
		}	
    },
    render: function() {
    	this.borrarViewsItems();
        this.collection.forEach(this.AddOne,this);
		this.actualizarImporteTotal();
        return this;
    },
    
    borrarViewsItems: function() {
    	var indice = 0;
    	if(window.views.inputs && window.views.inputs.length){
    		indice = window.views.inputs.length;
    	}else{
    		indice = 0;
    		window.views.inputs = [];
    	}
    	for(var cont = 0; cont < indice; cont++ ){
    		window.views.inputs[cont].remove();
    	}
    },
	actualizarImporteTotal: function(){
		var importe = window.collections.productos.importeTotal();
		var conFormato = formatNumber.new(importe,'$ ');
		$("#importeTotal").html('Importe total '+ conFormato);
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
