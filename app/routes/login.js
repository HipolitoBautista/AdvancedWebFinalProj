const express = require('express');
const router = express.Router();
const axios = require('axios');
const dotenv = require('dotenv').config();

router.get('/', function(request, response){
    if(request.session.userID){
        response.redirect("/home");
        console.log("Valid Session Found" + request.session.userID);
    }else if (!request.session.userID){
        response.render('login');
        console.log("login triggered");
    }
});

router.post('/', async (request,response) => {
	
	let username = request.body.username;
	let password = request.body.password;
	// TODO: Delete upon completion of program
	// console.log(request.body);
	// console.log("Username" + username + "Password" + password );
	console.log("login post triggered");
	let params = JSON.stringify(request.body);
	let config = {
		method: 'GET',
		maxBodyLength: Infinity,
		url: 'http://'+ process.env.HOSTIP +':8036/portal/' + request.body.username,
		headers:{"content-type":"application/json",
		"API-KEY":"awt_ab598017be4550411284958a4c2014ec56000457792e7b92307137b824a24d38_6ee865bfab165006ae13495fdcd6617c1c300c0b98aaa0b078e0ff52091a96ac"
	}, 
		data: params
	};

	//  V2
	userID = -1;
	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data);
		
		if(axiosResponse.data.id) {
			userID = axiosResponse.data.id; 
			request.session.userID = userID;		}	
		}catch(error){
			 console.error(error);
	}
	response.send(jsonString);	
});

module.exports=router;