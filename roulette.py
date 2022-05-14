import random


def ask_input(money):
    bet = []
    print("What numbers do you want to bet on?")

    while money > 0:
        task = input("")

        if task == "STOP":
            break

        if task not in str(list(range(37))):
            print(
                "Not a valid bet. Pick a number between 1 and 36 or type STOP.")

        else:
            task = int(task)
            bet.append(task)
            money -= 1
            if money == 0:
                print("You're out of chips.")
    return money, bet


def roulette(money):
    money, bet = ask_input(money)

    res = random.randint(1, 36)
    print(f"Rien ne va plus!\nThe result is {res}.")

    bank = 0 + money
    for x in bet:
        if x == res:
            bank += 35
    print(f"You have {bank} chips!")

    return bank


chips = 10

while chips > 0:
    chips = roulette(chips)

print("Game Over")
