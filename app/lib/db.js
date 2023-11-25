const mysql = require('mysql2');
const dotenv = require('dotenv').config();

const pool = mysql.createPool({
    // TODO: Uncomment when submitting
    host: process.env.DBHOST,
    user: process.env.DBUSER,
    password: process.env.DBPWD,
    database: process.env.DBNAME
}).promise()


async function getResources(){
    var result = [];

    try {
        let  sql = "select parent from permission where status = 1";
        [result] = await pool.query(sql);
    }catch(error){
        console.log(error);
    }

    return result;
}

async function sample(sessionID, score){
    var result = {
        "affectedRows": -1
    };

    try {
        [result] = await pool.query("update activity_session set rating=? where 'status' = 1 and session = ?", [score, sessionID])
    }catch(error){
        console.log(error);
    }

    return result;
}

module.exports = {
    getResources,
};