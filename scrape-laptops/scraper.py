from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
# I'll use Google Chrome
from bs4 import BeautifulSoup
# Pandas and MySQL for the database
import pandas as pd
import mysql.connector

options = webdriver.ChromeOptions()
options.add_experimental_option('excludeSwitches', ['enable-logging'])
# Below option makes sure the window stays open, so put it in if you need that!
# options.add_experimental_option("detach", True)
# ####

browser = webdriver.Chrome(options = options, service=Service(ChromeDriverManager().install()))
browser.get('https://www.amazon.nl')

#Find and click cookie-button
button = browser.find_element(By.ID, "sp-cc-accept")
button.click()

#Type in search bar and click search icon
searchbar = browser.find_element(By.ID, "twotabsearchtextbox")
searchbar.send_keys("laptops")
searchbutton = browser.find_element(By.ID, "nav-search-submit-button")
searchbutton.click()

#Find and click matching check boxes
checkbox = browser.find_element(By.ID, "p_n_feature_nineteen_browse-bin/16365376031")
checkbox.click()

#We're at page one, create a list of titles and prices
titles = []
prices = []

#This function finds the title- and price elements on a page. I found the classes with 'inspect'. If the value is 'none', it'll add 'not available' to the list.  
#Otherwise it removes the 'span' tags and adds the title or price.  
def lister(page):
    for laptop in page.find_all('div', {'class':'a-section a-spacing-small s-padding-left-small s-padding-right-small'}):
        laptopprice = laptop.find("span", {"class": "a-price-whole"})
        if laptopprice is not None:
                prices.append(laptopprice.text.strip())
        else:
            prices.append("Niet beschikbaar")
        laptopname = laptop.find("span", {"class": "a-size-base-plus a-color-base a-text-normal"})
        if laptopname is not None:
                titles.append(laptopname.text.strip())
        else:
            titles.append("Niet beschikbaar")

#This function waits for the page to load by checking if the current page number matches the target page number. Then it will execute the above function to find titles and prices. 
def pagescrape(pagenum):
    pagenum = str(pagenum)
    WebDriverWait(browser, 10).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, (f"[aria-label='Huidige pagina, pagina {pagenum}']")))
    )
    lister(BeautifulSoup(browser.page_source, 'html.parser'))

#This function switches to the new page the moment the old page is loaded. So from page 1 to page 2 (add '2' to the function). Then it runs the above function: waiting for the page
#to load, and extract the information. 
def pageswap(newpagenum):
    newpagenum = str(newpagenum)
    try: 
        element = WebDriverWait(browser, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, (f"[aria-label='Ga naar pagina {newpagenum}']")))
        )
        element.click()
    except:
        print(f"Couldn't find {newpagenum}")
    pagescrape(newpagenum)

#Page 1 manually, then we'll use the loop. 
# Ask the user which page is the last one they want to search on
finalpage = int(input("Until what page do you want to search for laptops?"))
pagescrape(1)
for num in range(2,finalpage+1):
    pageswap(num)

#And close the browser after it's done.
browser.close()

#Now I'll make a dictionary from the titles and prices. Then I'll print them with the name on one line and the price below.
price_dict = {"Titles": titles, "Prices": prices}
aantal = len(price_dict["Titles"])
print(f"There are {aantal} laptops.")
for i in range(aantal):
    key = price_dict["Titles"][i]
    value = price_dict["Prices"][i]
    print(f"Name: {key}\nPrice: {value}")

#I'll use pandas to make a dataframe for easier loading into the database. 
df = pd.DataFrame(price_dict)
#I'll remove the laptops that say 'not available' in any column. 
df =df[(df.Titles != "Niet beschikbaar") & (df.Prices != "Niet beschikbaar")]

#And now on to the database. Add your own username and password here if you want to run. 
mydb = mysql.connector.connect(
  host="localhost",
  user="username_here",
  password="password_here"
)

cursor = mydb.cursor()

cursor.execute("DROP DATABASE IF EXISTS Laptops")
cursor.execute("CREATE DATABASE IF NOT EXISTS Laptops")
cursor.execute("USE Laptops")

cursor.execute(
    "CREATE TABLE IF NOT EXISTS found_laptops"
    "(model VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL)"
    )

for row in df.itertuples(index=False):
      sql = "INSERT INTO found_laptops (model, price) values (%s, %s)"
      cursor.execute(sql, row)
      mydb.commit()
print("File loaded succesfully!")