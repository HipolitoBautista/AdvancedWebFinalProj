const express = require('express');
const db = require("../lib/db");
const rest = require("../lib/rest");
const axios = require('axios');
const dotenv = require('dotenv').config();


const router = express.Router();

router.all('/:resourceID/:ID?', async(request,response) => {
    console.log(request.originalUrl);
        var data;
        switch(request.method){
        case "GET":
            data = await rest.getInvoked(request, response);
            response.send(data);
            break;
        case "POST":
            data = await rest.postInvoked(request, response);
            response.send(data);
            break;
        case "PUT":
            data = await rest.putInvoked(request, response);
            response.send(data);
            break;
        case "DELETE":
            data = await rest.deleteInvoked(request, response);
            response.send(data);
            break;
        default:
            response.send("Invalid HTTP Method");      
    }
})
module.exports=router;
