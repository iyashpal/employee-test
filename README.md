# HeadRed Coding Test

## Guide

### Local machine setup (macos/windows)

#### Required tools to run the code
- PHP preprocessor
- Composer
- MySQL or SQlite
- Node.js LTS version
- `npm` or `bun` or `yarn` node package manager (I have used `bun`)

OR 

- [Laravel Herd](https://herd.laravel.com/) (available for windows/macos)

OR

- [Docker desktop](https://www.docker.com/) (available for windows/macos)

#### Setup Steps

- Use the `git clone` command (`git clone https://github.com/iyashpal/laravel-test.git`) to pull the code into your system. 
- Check out to branch name `feature/sync-reqres-users` using command `git checkout feature/sync-reqres-users`
- Duplicate `.env.example` file to `.env`
- For quick setup I used sqlite connection. If you want to use the same update following `.env` variables. OR you can change the database drivers listed in `config/database.php` config file.
```dotenv
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```
- Update `REQRES_BASE_URL` environment variable with following setting. 
```dotenv
REQRES_BASE_URL=https://reqres.in
```
- Now, install composer dependencies using command `composer install`.
- Next, generate database tables by running the migration command `php artisan migrate`.
- One last piece of puzzle is the node dependencies installation. Install node dependencies using command `npm install` or `yarn install` or `bun install`. Choose one that you are using. Mine was `bun install`.

#### Run Code

- I personally use [laravel herd](https://herd.laravel.com/) on windows and mac. If you are using the same then you will get auto generated url. Please configure the code with laravel herd.

OR

- If you prefer to use docker then laravel sail is installed and configured. Run `./vendor/bin/sail up` command to run the project or refer to this [page](https://laravel.com/docs/11.x/sail).

OR

- If you are none of above and have required tools installed on your system (PHP Preprocessor, Composer, Node.js) then simple run this command from project root `php artisan serve`. You can now access the project on url `127.0.0.1:8000`.


Now try to access the app on browser:

- Oops, got the vite manifest error? Not to worry run vite dev server by using command `npm run dev` OR `yarn dev` OR `bun dev`.

🎉 You must see the login screen now.

#### Setup Login User
- Navigate to url `/register` and register your self.
- `MustVerifyEmail` is implemented to `User` model so you have to manually verify your account in users table.
- To mark user as verified put DateTime format string in `email_verified_at` column and you are ready to login. If you login without verify it will show a re-send verification mail page.
- Once you mark the user verified you can login with the credentials you entered while registration.

Can see the dashboard?
> 🎉Congratulations🎊 project setup is successful now.

- Want to run the scheduler?
> If yes, all you have to setup the every minutes cron job which should run the artisan command `php artisan schedule:run`.
> And in case you want to run it locally run `php artisan schedule:work`. You will get realtime command execution in terminal.

## Questions & Answers
- Is your code testable?

> Yes, I have tried to cover everything in tests. Added architecture unit tests presets as well as the feature tests. 

- What happens if the API is unavailable?
> If anything wrong happens with the APIs the command will exit with 0 code. As we will be using the scheduler to fetch the data using command line so it won't affect the app. 
- If we wanted to add more searchable fields in the future, this should be an easy task.
> Searchable fields are fully configurable. All we just need is to add/remove the field in search dropdown. 
- When the scheduled task runs, what happens if the user already exists?
> In users schema the email is the unique field. So when the scheduled task fetches users it will look for existing records against the user email.
> If the user against the email is already exists it will update the other fields like `first_name`, `last_name`, `avatar`, `password` and `source`.
> `source` field is an identifier which will hold the state whether the user is created from `reqres` or `app`
- If we wanted to change the API to use a different service, how difficult should that be?
> I have created Reqres service class which includes some methods and properties. All we just need is to configure the api base in `.env` file and if required replace api endpoint in `getUsers` method within the service class.
> Currently the service class name is identical to Reqres but if needed we can pick a common name for it (i.e. UserDirectoryService). 
