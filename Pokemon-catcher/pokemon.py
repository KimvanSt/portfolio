import mysql.connector
import requests

# Activate the MySQL connection
# !! ENTER YOUR DATABASE USERNAME AND PASSWORD HERE !!
mydb = mysql.connector.connect(
    host="localhost",
    user="enter-username-here",
    password="enter-password-here"
)

mycursor = mydb.cursor()

# Check if database and table exists, if not, create those
mycursor.execute("CREATE DATABASE IF NOT EXISTS caught_pokemon")
mycursor.execute("USE caught_pokemon")
mycursor.execute(
    "CREATE TABLE IF NOT EXISTS pokemon (ID INT(255), Name VARCHAR(255), Weight INT(255), Height INT(255))")

# Defining the data I want to gather
keys = ["id", "name", "weight", "height"]

# For Pokémon with ID's 1 through 151....
index = range(1, 152, 1)

# ... I will gather the right webpage. Then I will go through the info and for each topic in my 'keys'-list, gather the
#       data. I will save it in my 'values'-list.
#       Then I prepare my 'insert into' statement for these values. The print statement tracks my progress,
#       printing each Pokémon's data after retrieval.
for n in index:
    mon = requests.get(f'https://pokeapi.co/api/v2/pokemon/{n}')
    for item in mon:
        values = []
        for entry in keys:
            values += [mon.json()[entry]]
    sql = "INSERT INTO pokemon(ID,Name,Weight,Height) values (%s, %s, %s, %s)"
    mycursor.execute(sql, values)
    print(f"Prepared {values} for entry")

# Finally, I commit my entries and at the end print that my program is done running.
mydb.commit()
print('Database updated.')
