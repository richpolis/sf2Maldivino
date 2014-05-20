window.Usuarios = {};

Usuarios.Views = {};
Usuarios.Collections = {};
Usuarios.Models = {};
Usuarios.Routers = {};

window.routers = {};
window.models = {};
window.views = {};
window.collections = {};


Usuarios.Models.Usuario = Backbone.Model.extend({
    defaults: {
      username: '',
      nombre: '',
      email: '',
      grupo: 1,
      twitter: '',
      facebook: '',
      imagen: '',
      salt: ''
    }
});

Usuarios.Collections.Usuarios = Backbone.Collection.extend({
    model: Usuarios.Models.Usuario,
});

//esta vista recibe un modelo UsuarioModel para que pueda renderear su contenido
Usuarios.Views.Item = Backbone.View.extend({

    tagName: "tr",
    
    template: swig.compile($("#item_table_template").html()),
    
    initialize: function() {
        this.model.bind("change", this.render, this);
        this.model.bind("destroy", this.close, this);
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data)).attr({id: 'registro-'+this.model.get('id')});
        return this;
    }

});

//esta vista es para visualizar el show_template
Usuarios.Views.Show = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    template: swig.compile($("#show_template").html()),

    events: {
        'click #botonRegresar': 'regresar',
        'click #botonEliminar' : 'eliminar',
    },
    initialize: function() {
        this.model.on("change", this.render, this);
        window.api.modelo.id = this.model.get('id');
    },
    regresar : function(e){
        e.preventDefault();
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    eliminar : function(e){
        $(".loader").fadeIn();
        e.preventDefault();
        e.stopPropagation();
        if(!window.api.accion.eliminar && window.api.modelo.id == this.model.get('id')){
            window.api.accion.eliminar = true;
            console.log("Accion eliminar");
            this.model.urlRoot = window.api.url;
            this.model.destroy({
                success: function(){
                    window.collections.usuarios.reset();
                    var xhr = window.collections.usuarios.fetch();
                    xhr.done(function(){
                        window.routers.app.navigate('/',true);
                        $(".loader").fadeOut();
                        window.api.accion.eliminar = false;
                    });
                }
            });
        }
    },
    render: function() {
        var data = this.model.toJSON();
        this.$el.html(this.template(data));
        return this;
    }

});

//esta vista es para visualizar el formulario
Usuarios.Views.FormNew = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'change input[name=file]': 'changeImage',
        'change': 'change',
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'beforeSave',
        
    },
    initialize: function(data) {
        this.contenido = data;
        window.api.modelo.id = 0;
    },
    regresar : function(e){
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    change: function (event) {
        debugger;
        // Apply the change to the model
        var target = event.target;
        var change = {};
        change[target.name] = target.value;
        this.model.set(change);
    },
    beforeSave: function (e) {
        e.preventDefault();
        e.stopPropagation();
        var self = this;
        var check = this.validarForm();
        if (!check) {
            return false;
        }
        if (this.pictureFile) {
            this.model.set("imagen", this.pictureFile.name);
            this.uploadFile(this.pictureFile,
                function () {
                    self.saveUser();
                }
            );
        } else {
            this.saveUser();
        }
        return false;
    },
    saveUser: function () {
        var self = this;
        if(!window.api.accion.guardar  && window.api.modelo.id == 0){
            window.api.accion.guardar=true;
            $.ajax({
               url: window.api.url,
               data: $("#formUsuarios").serialize(),
               dataType: 'json',
               type: 'POST',
               success: function(data, textStatus,jqXHR){
                        debugger;
                        window.api.accion.guardar=false;
                        console.log("Creado")
                        console.log(data)
                        var model = new Usuarios.Models.Usuario(data);
                        window.collections.usuarios.add(model);
                        window.routers.app.navigate("show/"+model.get('id'),true);
                        $(".loader").fadeOut();
                },
                error: function(data, textStatus,jqXHR){
                    debugger;
                    window.api.accion.guardar=false;
                    $(".loader").fadeOut();
                    console.log(data);
                    window.routers.app.parseErrores(data);
                }
            });
        }else{
            window.api.accion.guardar = false;
            $(".loader").fadeOut();
        }
    },
    render: function() {
        this.$el.html(this.contenido.data);
        return this;
    },
    validarForm: function(){
        var exprPassword = /(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{8,10})$/;
        var exprEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var password = {
            'first':$("#password_first").val(),
            'second':$("#password_second").val(),
        }
        var $nombre = $("input#nombre");
        var $email = $("input#email");
        
        if(password.first.length <= 3){
            window.routers.app.errorNoty("El password no es correcto.");
            $("input#password_first").focus();
            return false;
        }else if(password.first!=password.second){
            window.routers.app.errorNoty("Las contraseñas deben coincidir.");
            $("input#password_first").focus();
            return false;
        }else if(!exprPassword.test(password.first)){
            window.routers.app.errorNoty("La contraseña debe de ser 6 a 8 caracteres, minimo un digito, sin caracteres especiales.");
            $("input#password_first").focus();
            return false;
        }
        
        
        // validar el nombre 
        if($nombre.val().length == 0){
            window.routers.app.errorNoty("El nombre esta vacio.");
            $nombre.focus();
            return false;
        }else if($nombre.val().length < 3){
            window.routers.app.errorNoty("El nombre es demasiado corto, minimo 3 caracteres");
            $nombre.focus();
            return false;
        }
        
        
        if ( !exprEmail.test($email.val()) ){
            window.routers.app.errorNoty("El email es incorrecto.");
            $email.focus();
            return false;
        }
        return true;
    },
    changeImage: function (event) {
        event.stopPropagation();
        event.preventDefault();
        this.pictureFile = event.target.files[0];

        // Read the image file from the local file system and display it in the img tag
        var reader = new FileReader();
        reader.onloadend = function () {
            $('#picture').attr('src', reader.result);
        };
        reader.readAsDataURL(this.pictureFile);
    },
    uploadFile: function (file, callbackSuccess) {
        debugger;
        var self = this;
        var data = new FormData();
        data.append('qqfile', file);
        $.ajax({
            url: window.api.url + "/uploads",
            type: 'POST',
            data: data,
            processData: false,
            cache: false,
            contentType: false
        })
        .done(function (data) {
            debugger;
            console.log(data);
            if(data.filename){
                self.model.set({imagen: data.filename});
                self.$el.find("input#imagen").val(data.filename);
                callbackSuccess();
            }else{
                alert("error en uploadFile");
            }
        })
        .fail(function (data) {
            debugger;
            window.routers.app.errorNoty(data.error);
            console.log(data);
        });
    },

});

//esta vista es para visualizar el formulario
Usuarios.Views.FormEdit = Backbone.View.extend({

    /*el: '#contenidoPagina',*/
    
    events: {
        'change input[name=file]': 'changeImage',
        'change': 'change',
        'click #botonRegresar': 'regresar',
        'click #botonGuardar' : 'beforeSave',
        'click #botonEliminar' : 'eliminar',
    },
    
    initialize: function(data) {
        this.contenido = data;
        window.api.modelo.id = this.model.get('id');
    },
    
    regresar : function(e){
        e.stopPropagation();
        console.log("regresar");
        window.routers.app.navigate('/',true);
    },
    
    change: function (event) {
        debugger;
        // Apply the change to the model
        var target = event.target;
        var change = {};
        change[target.name] = target.value;
        this.model.set(change);
        
    },

    beforeSave: function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".loader").fadeIn();
        debugger;
        var self = this;
        var check = this.validarForm();
        if (!check) {
            return false;
        }
        // Upload picture file if a new file was dropped in the drop area
        if (this.pictureFile) {
            this.model.set("imagen", this.pictureFile.name);
            this.model.set("imagenOld", this.model.get('imagen'));
            this.uploadFile(this.pictureFile,
                function () {
                    self.saveUser();
                }
            );
        } else {
            this.saveUser();
        }
        $(".loader").fadeOut();
        return false;
    },

    saveUser: function () {
        $(".loader").fadeIn();
        if(!window.api.accion.guardar && window.api.modelo.id == this.model.get('id')){
            window.api.accion.guardar = true;
            var self = this;
            $.ajax({
                url: window.api.url+"/"+this.model.get("id"),
                data: $("#formUsuarios").serialize(),
                dataType: 'json',
                type:'PUT',
                success: function(data, textStatus, jqXHR){
                        debugger;
                        window.collections.usuarios.reset();
                        var xhr = window.collections.usuarios.fetch();
                        xhr.done(function(){
                            window.routers.app.navigate('show/'+window.api.modelo.id,true);
                            window.api.accion.guardar = false;
                            $(".loader").fadeOut();
                        });
                },
                error: function(data, textStatus, errorThrown){
                       debugger;
                       $(".loader").fadeOut();
                       console.log($.parseJSON(textStatus.responseText));
                       window.routers.app.parseErrores(textStatus);
                       window.api.accion.guardar = false;
                }
            });
            
        }else{
           window.api.accion.guardar = false;
           $(".loader").fadeOut();
        }
    },
    
    eliminar : function(e){
        var self = this;
        $(".loader").fadeIn();
        e.preventDefault();
        e.stopPropagation();
        if(!window.api.accion.eliminar  && window.api.modelo.id == this.model.get('id')){
            window.api.accion.eliminar = true;
            console.log("Accion eliminar");
            this.model.urlRoot = window.api.url;
            this.model.destroy({
                success: function(){
                    window.collections.usuarios.reset();
                    var xhr = window.collections.usuarios.fetch();
                    xhr.done(function(){
                        window.routers.app.navigate('/',true);
                        window.api.accion.eliminar = false;
                        $(".loader").fadeOut();
                    });
                }
            });
        }
    },
    
    render: function() {
        this.$el.html(this.contenido.data);
        return this;
    },

    validarForm: function(){
        var exprPassword = /(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{8,10})$/;
        var exprEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var password = {
            'first':$("#password_first").val(),
            'second':$("#password_second").val(),
        }
        var $nombre = $("input#nombre");
        var $email = $("input#email");
        if(password.first.length>0 && password.second.length==0){
            $("#password_first").val("");
        }else{
            if(password.first!=password.second){
                window.routers.app.errorNoty("Las contraseñas deben coincidir.");
                $("input#password_first").focus();
                return false;
            }else if(password.first.length>0){
                if(!exprPassword.test(password.first)){
                    window.routers.app.errorNoty("La contraseña debe de ser 6 a 8 caracteres, minimo un digito, sin caracteres especiales.");
                    $("input#password_first").focus();
                    return false;
                }
            } 
        }
        // validar el nombre 
        if($nombre.val().length == 0){
            window.routers.app.errorNoty("El nombre esta vacio.");
            $nombre.focus();
            return false;
        }else if($nombre.val().length < 3){
            window.routers.app.errorNoty("El nombre es demasiado corto, minimo 3 caracteres");
            $nombre.focus();
            return false;
        }
        
        if ( !exprEmail.test($email.val()) ){
            window.routers.app.errorNoty("El email es incorrecto.");
            $email.focus();
            return false;
        }
        return true;
    },
    changeImage: function (event) {
        debugger;
        event.stopPropagation();
        event.preventDefault();
        this.pictureFile = event.target.files[0];

        // Read the image file from the local file system and display it in the img tag
        var reader = new FileReader();
        reader.onloadend = function () {
            $('#picture').attr('src', reader.result);
        };
        reader.readAsDataURL(this.pictureFile);
    },
    uploadFile: function (file, callbackSuccess) {
        debugger;
        var self = this;
        var data = new FormData();
        data.append('qqfile', file);
        $.ajax({
            url: window.api.url +  "/uploads",
            type: 'POST',
            data: data,
            processData: false,
            cache: false,
            contentType: false
        })
        .done(function (data) {
            debugger;
            console.log(data);
            if(data.filename){
                self.model.set({imagen: data.filename});
                self.$el.find("input#imagen").val(data.filename);
                callbackSuccess();
            }else{
                alert("error en uploadFile");
            }
        })
        .fail(function (data) {
            debugger;
            window.routers.app.errorNoty(data.error);
            console.log(data);
        });
    },


});


//vista collecio que recibe todos los modelos.
Usuarios.Views.Table = Backbone.View.extend({

    el: '#contenidoPagina',
    
    template: swig.compile($("#table_template").html()),
    
    initialize: function() {

        this.cuerpoTabla = "#cuerpoTabla";
        
        this.collection.on("add", this.AddOne,this);
        //esta es para que ejecute el render inicial
        //this.render();
    },
    
    AddOne: function(usuario) {
        var usuarioView = new Usuarios.Views.Item({model: usuario});
        var html = usuarioView.render().el;
        $(this.cuerpoTabla).append(html);
    },
    
    render: function() {
        this.$el.html(this.template());
        this.collection.forEach(this.AddOne,this);
        this.inicializarDatatable();
        return this;
    },
    
    inicializarDatatable: function() {
        $('#datatable').dataTable({
            "sPaginationType": "full_numbers",
            "sNext": "Siguiente",
            "sLast": "Ultimo",
            "sFirst": "Primero",
            "sPrevious": "Anterior",
        });
    },
});

Usuarios.Routers.App = Backbone.Router.extend({
    routes: {
        "" : "root",
        "new" : "new",
        "edit/:id" : "edit",
        "show/:id" : "show",
    },
    
    root: function() {
        window.views.table = new Usuarios.Views.Table({
            collection: window.collections.usuarios,
        });
        views.table.render();
    },
    
    new: function() {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET',
           url: window.api.url + "/new",
           dataType: 'html',
        }).done(function(data){
            debugger;
            if(window.views.formEdit)window.views.formEdit.remove();
            if(window.views.show)window.views.show.remove();
            var model = new Usuarios.Models.Usuario();
            model.urlRoot = window.api.url;
            model.set({salt: $(data).find('input#salt').val()});
            window.views.formNuevo = new  Usuarios.Views.FormNew({data: data, model: model});
            $("#contenidoPagina").html(views.formNuevo.render().el).fadeIn("fast");
            $(".loader").fadeOut();
            
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de nuevo registro");
        });
    },
    edit: function(id) {
        $(".loader").fadeIn();
        $.ajax({
           type: 'GET', 
           url: window.api.url + "/" +id+"/edit",
           dataType: 'html',
        }).done(function(data){
            debugger;
            if(window.views.formNuevo)window.views.formNuevo.remove();
            if(window.views.show)window.views.show.remove();
            var model = window.collections.usuarios.get(id);
            model.urlRoot = window.api.url;
            window.views.formEdit = new  Usuarios.Views.FormEdit({data: data, model: model});
            $("#contenidoPagina").html(views.formEdit.render().el).fadeIn("fast");
            $(".loader").fadeOut();
        }).fail(function(){
            $(".loader").fadeOut();
            alert("Error al cargar la pagina de editar registro");
        });
    },
    show: function(id) {
        debugger;
        if(window.views.formNuevo)window.views.formNuevo.remove();
        if(window.views.formEdit)window.views.formEdit.remove();
        var model = window.collections.usuarios.get(id);
        window.views.show = new  Usuarios.Views.Show({model: model});
        $("#contenidoPagina").html(views.show.render().el).fadeIn("fast");
    },
    loadFormData: function(formData, formElement){
        formData.append("username", formElement.username.value);
        formData.append("password", {first: formElement.password_first.value,second: formElement.password_second.value});
        formData.append("email", formElement.email.value);
        formData.append("email", formElement.email.value);
        formData.append("nombre", formElement.nombre.value);
        formData.append("file", formElement.file.files[0]);
        formData.append("twitter", formElement.twitter.value);
        formData.append("facebook", formElement.facebook.value);
        formData.append("grupo", formElement.grupo.value);
        formData.append("imagen", formElement.imagen.value);
        formData.append("salt", formElement.salt.value);
        formData.append("_token", formElement._token.value);
        
        return formData;
    },

    errorNoty: function(mensaje){
        noty({
            text: mensaje,
            layout:'topRight',
            type:'error',
            timeout:2000
        });
    },
    
    parseErrores: function(data){
        debugger;
        var json = $.parseJSON(data.responseText);
        var children = json.errors.children;
        var stringError ="";
        var cont=0;
        var contb=0;
        for(cont = 0; cont< children.length;  cont++){
            if(children[cont]['errors']){
                for(contb=0; contb<children[cont]['errors'];contb++){
                    stringError +=children[cont]['errors'][contb];
                }
            }
        }
        this.errorNoty(stringError);
    }
});


