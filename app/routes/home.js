const express = require('express');
const db = require("../lib/db");
const rest = require("../lib/rest");
const axios = require('axios');
const dotenv = require('dotenv').config();

const router = express.Router();

router.get("/", async(request, response) => {
    if(request.session.userID){
        console.log("Valid Session Found" + request.session.userID);
        const list = await db.getResources();
        response.render("home", {resources: list});
    } else {
        response.redirect("/login");
    }
});

router.get('/:resourceID', async(request,response) => {
    if(request.session.userID){
        response.render("endpoints", {endpointName: request.params.resourceID});
	}else {
		response.redirect('/login');
	}
})

module.exports=router;
