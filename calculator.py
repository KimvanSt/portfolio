# Ask for +, -, /, * or %
operatie = input("What would you like to do? (Pick +, -, *, / or %.)\n")
if operatie not in "+-*/%":
    print(operatie + " is not a valid request. Pick +, -, *, / or %.")
    exit()
# Ask for first number
first = input("First number?\n")
for entry in first:
    try:
        firstNumber = float(first)
    except ValueError:
        print("Can't use this input as a number. Use a period, not a comma!")
        exit()
    else:
        continue
# Ask for second number
second = input("Second number?\n")
for entry in second:
    try:
        secondNumber = float(second)
    except ValueError:
        print("Can't use this input as a number. Use a period, not a comma!")
        exit()
    else:
        continue
# Addition
if operatie == "+":
    print("Your addition is:", firstNumber + secondNumber)
# Subtraction
elif operatie == "-":
    print("Your subtraction is:", firstNumber - secondNumber)
# Vermenigvuldigen
elif operatie == "*":
    print("Your multiplication is:", firstNumber * secondNumber)
# Modulo
# The second number cannot be 0
elif operatie == "%" and secondNumber == 0:
    print("Cannot divide by 0")
elif operatie == "%":
    print("Your modulo is:", firstNumber % secondNumber)
# Delen
# The second number cannot be 0
elif operatie == "/" and secondNumber == 0:
    print("Cannot divide by 0")
elif operatie == "/":
    print("Your division is:", firstNumber / secondNumber)
