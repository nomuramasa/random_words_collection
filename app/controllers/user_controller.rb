class UserController < ApplicationController

  # ログインが必要です
  before_action :authenticate_user, {only: [:index, :show, :edit, :update, :delete]}

  # すでにログインしています
  before_action :alread_login, {only: [:new, :create, :login, :login_form]}

  # 管理者用のページは見れません
  before_action :only_owner, {only: [:index]}

  # 他のユーザーの情報は、閲覧や編集はできません
  before_action :cannot_edit, {only: [:show, :edit, :update]}


 	# ユーザー一覧
  def index
  	@users = User.all
  end

 	# ユーザー詳細
  def show
  	@user = User.find_by(id:params[:id])
  end

 	# 新規登録
  def new
  	@user = User.new # 空定義
  end

  def create
  	@user = User.new(
      name: params[:name], 
      email: params[:email],
      password: params[:password]
    )
  	if @user.save # 保存できたら、登録成功
      flash[:notice] = 'ユーザーを登録しました'
			session[:user_id] = @user.id # ログイン状態へ

      cookies[:new_registration] = '1'  # 新規登録フラグを立てる（データ移行JSを読み込む為）

      redirect_to("/user/#{@user.id}") # 詳細ページへ
    else # 保存できなかったら、登録失敗
      render('user/new') # newアクションを経由せずに（createアクションの@userデータを持ったまま）直接、新規登録画面に
    end
  end


  # ブラウザのストレージからRailsのDBへ引き継ぎ（新規登録時のみ）
  def move_data

    @word = Word.new( # データベースに保存
      content: params[:name],
      star: params[:star],
      visit: params[:visit],
      user_id: @current_user.id 
    )
    @word.save
  end


  # ユーザー編集
  def edit
  	@user = User.find_by(id:params[:id])
  end

  def update
  	@user = User.find_by(id:params[:id])
  	@user.name = params[:name]
    @user.email = params[:email]
    @user.password = params[:password]

  	if @user.save # 成功
  		flash[:notice] = 'ユーザー情報を変更しました'
  		redirect_to("/user/#{@user.id}")
  	else # 失敗
  		render("user/edit")
  	end
  end

  # ユーザー削除
  def delete
    @user = User.find_by(id:params[:id]) 
    @words = Word.where(user_id: @user.id) #そのユーザーの単語全部
    @words.destroy_all
    @user.destroy # ユーザー削除
    flash[:notice] = '退会しました'
    session[:user_id] = nil
	  redirect_to("/") # トップへ
  end




  # ログインページ
  def login_form
  end

  # ログイン
  def login
    @email = params[:email] # 失敗しても初期値にする
    @password = params[:password] # 失敗しても初期値にする

    @user = User.find_by(email: @email) # POSTで送られたemail値のユーザーをDBから探す
    if @user # メールアドレスが存在
      if @user && @user.authenticate(@password) # メール・暗号化されたパスワード、両方一致 
     
        session[:user_id] = @user.id
        flash[:notice] = 'ログインしました'
        redirect_to('/')
      else
        @error_message = 'パスワードが間違っています'
        render('/user/login_form')
      end
    else
      @error_message = 'そのメールアドレスは登録されていません' 
      render('/user/login_form')
    end
  end

  # ログアウト
  def logout
    session[:user_id] = nil
    flash[:notice] = 'ログアウトしました'
    redirect_to('/')
  end


end
