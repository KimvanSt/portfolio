numbers = range(1, 101, 1)

for n in numbers:
    if n%3 == 0 and n%5 == 0:
        print("FizzBuzz")
    elif n%3 == 0:
        print ("Fizz")
    elif n%5 == 0:
        print("Buzz")
    else:
        print(n)

