# Wizualizacja Danych Meteorologicznych

**Wizualizacja Danych Meteorologicznych** to prosta aplikacja webowa umożliwiająca przeglądanie historycznych wykresów danych meteorologicznych dla miast **Warszawa** oraz **Kraków**. Dzięki niej możesz analizować kluczowe parametry pogodowe, co pozwala na łatwe porównanie oraz wizualizację trendów w danym okresie.

## Funkcjonalności

- **Wizualizacja Danych**
  Prezentacja wykresów dotyczących:
  - Temperatury
  - Opadów
  - Wilgotności
  - Prędkości wiatru
  - Ciśnienia atmosferycznego

- **Zakres danych**
  Dane meteorologiczne obejmują okres od **2019-01-01** do **2024-08-20**.

## Architektura

Projekt oparty jest na konteneryzowanej architekturze, składającej się z trzech głównych usług:

- **nginx**
  Serwer WWW, który wystawia aplikację na porcie **80**.

- **php-fpm**
  Interpreter PHP obsługujący logikę aplikacji oraz przetwarzanie zapytań.

- **mariadb**
  Baza danych MariaDB przechowująca historyczne dane meteorologiczne.

## Uruchomienie

Aby uruchomić projekt, wystarczy wykonać:

```bash
docker compose up
```

Aplikacja będzie dostępna pod adresem: [http://localhost](http://localhost).

## Dostępne Miasta

- Warszawa
- Kraków

## Autorzy

- Dawid Maliszewski
- Adam Ropelewski
