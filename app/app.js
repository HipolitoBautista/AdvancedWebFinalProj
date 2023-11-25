const bodyParser = require('body-parser');
var express = require('express');
var session = require('express-session');
var app = express();

app.use(session({
  secret: 'your-secret-key', 
  resave: false,
  saveUninitialized: true
}));

app.use(bodyParser.json());
app.use(express.static(__dirname + '/public'));
app.use('*/css',express.static('public/css'));
app.use('*/js',express.static('public/js'));
app.use('*/images',express.static('public/images'));

app.set('view engine', 'ejs');


// Declaring different routes
const routeHome = require('./routes/home');
app.use('/home', routeHome);

const routeEndpoints = require('./routes/endpoints');
app.use('/endpoints', routeEndpoints);

const routeLogin = require('./routes/login');
app.use('/login', routeLogin);


app.get("/endpoints", (request,response) => {
	response.render("endpoints");
})

app.get("/", (request,response) => {
	if(request.session.userID){
		response.redirect('/home');
	}else {
		response.redirect('/login');
	}
})

app.get("/logout", (request,response) => {
	request.session.destroy();
	response.redirect('/login');
})

var server = app.listen(8038,function(){
	console.log("app running and listening on por %s\n", server.address().port);
})
