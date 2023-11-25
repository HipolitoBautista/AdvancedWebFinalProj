const mysql = require('mysql2');
const dotenv = require('dotenv').config();
const axios = require('axios');


let config = {
    maxBodyLength: Infinity,

    headers:{"content-type":"application/json",
    "API-KEY":"awt_ab598017be4550411284958a4c2014ec56000457792e7b92307137b824a24d38_6ee865bfab165006ae13495fdcd6617c1c300c0b98aaa0b078e0ff52091a96ac"
    }
};

async function getCourses() {
    let params = JSON.stringify({username: "tjoinson9", password: "james", });
    
    config.data = params;
    config.method = "get";
    config.url = 'http://'+ process.env.HOSTIP +':8036/applications';

	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data, undefined, 2);	
		}catch(error){
			 console.error(error);
	}

    return jsonString;
}

async function getInvoked(request, response) {
    
    var EndpointURI = request.originalUrl.replace("/endpoints", "");

    config.method = "GET";
    config.url = 'http://'+ process.env.HOSTIP +':8036/' + EndpointURI;

	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data, undefined, 2);	
		}catch(error){
			 console.error(error);
	}

    return jsonString;
}

async function postInvoked(request, response) {
    
    var EndpointURI = request.originalUrl.replace("/endpoints", "");
    config.data = request.body;
    config.method = "POST";

    config.url = 'http://'+ process.env.HOSTIP +':8036/' + EndpointURI;

	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data, undefined, 2);	
		}catch(error){
			 console.error(error);
	}

    return jsonString;
}

async function putInvoked(request, response) {
    
    var EndpointURI = request.originalUrl.replace("/endpoints", "");
    config.data = request.body;
    config.method = "PUT";

    config.url = 'http://'+ process.env.HOSTIP +':8036/' + EndpointURI;

	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data, undefined, 2);	
		}catch(error){
			 console.error(error);
	}

    return jsonString;
}

async function deleteInvoked(request, response) {
    
    var EndpointURI = request.originalUrl.replace("/endpoints", "");
    config.method = "DELETE";

    config.url = 'http://'+ process.env.HOSTIP +':8036/' + EndpointURI;

	let jsonString = "";

	try{
		const axiosResponse = await axios.request(config);
		jsonString = JSON.stringify(axiosResponse.data, undefined, 2);	
		}catch(error){
			 console.error(error);
	}

    return jsonString;
}
module.exports = {
    getCourses,getInvoked, postInvoked,putInvoked,deleteInvoked
}

    